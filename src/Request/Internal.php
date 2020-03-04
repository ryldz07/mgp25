<?php

namespace InstagramAPI\Request;

use GuzzleHttp\Psr7\LimitStream;
use GuzzleHttp\Psr7\Stream;
use function GuzzleHttp\Psr7\stream_for;
use InstagramAPI\Constants;
use InstagramAPI\Exception\CheckpointRequiredException;
use InstagramAPI\Exception\ConsentRequiredException;
use InstagramAPI\Exception\FeedbackRequiredException;
use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Exception\LoginRequiredException;
use InstagramAPI\Exception\NetworkException;
use InstagramAPI\Exception\SettingsException;
use InstagramAPI\Exception\ThrottledException;
use InstagramAPI\Exception\UploadFailedException;
use InstagramAPI\Media\MediaDetails;
use InstagramAPI\Media\Video\FFmpeg;
use InstagramAPI\Media\Video\InstagramThumbnail;
use InstagramAPI\Media\Video\VideoDetails;
use InstagramAPI\Request;
use InstagramAPI\Request\Metadata\Internal as InternalMetadata;
use InstagramAPI\Response;
use InstagramAPI\Signatures;
use InstagramAPI\Utils;
use Winbox\Args;

/**
 * Collection of various INTERNAL library functions.
 *
 * THESE FUNCTIONS ARE NOT FOR PUBLIC USE! DO NOT TOUCH!
 */
class Internal extends RequestCollection
{
    /** @var int Number of retries for each video chunk. */
    const MAX_CHUNK_RETRIES = 5;

    /** @var int Number of retries for resumable uploader. */
    const MAX_RESUMABLE_RETRIES = 15;

    /** @var int Number of retries for each media configuration. */
    const MAX_CONFIGURE_RETRIES = 5;

    /** @var int Minimum video chunk size in bytes. */
    const MIN_CHUNK_SIZE = 204800;

    /** @var int Maximum video chunk size in bytes. */
    const MAX_CHUNK_SIZE = 5242880;

    /**
     * UPLOADS A *SINGLE* PHOTO.
     *
     * This is NOT used for albums!
     *
     * @param int                   $targetFeed       One of the FEED_X constants.
     * @param string                $photoFilename    The photo filename.
     * @param InternalMetadata|null $internalMetadata (optional) Internal library-generated metadata object.
     * @param array                 $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     *
     * @see Internal::configureSinglePhoto() for available metadata fields.
     */
    public function uploadSinglePhoto(
        $targetFeed,
        $photoFilename,
        InternalMetadata $internalMetadata = null,
        array $externalMetadata = [])
    {
        // Make sure we only allow these particular feeds for this function.
        if ($targetFeed !== Constants::FEED_TIMELINE
            && $targetFeed !== Constants::FEED_STORY
            && $targetFeed !== Constants::FEED_DIRECT_STORY
        ) {
            throw new \InvalidArgumentException(sprintf('Bad target feed "%s".', $targetFeed));
        }

        // Validate and prepare internal metadata object.
        if ($internalMetadata === null) {
            $internalMetadata = new InternalMetadata(Utils::generateUploadId(true));
        }

        try {
            if ($internalMetadata->getPhotoDetails() === null) {
                $internalMetadata->setPhotoDetails($targetFeed, $photoFilename);
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                sprintf('Failed to get photo details: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        // Perform the upload.
        $this->uploadPhotoData($targetFeed, $internalMetadata);

        // Configure the uploaded image and attach it to our timeline/story.
        $configure = $this->configureSinglePhoto($targetFeed, $internalMetadata, $externalMetadata);

        return $configure;
    }

    /**
     * Upload the data for a photo to Instagram.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException
     */
    public function uploadPhotoData(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        // Make sure we disallow some feeds for this function.
        if ($targetFeed === Constants::FEED_DIRECT) {
            throw new \InvalidArgumentException(sprintf('Bad target feed "%s".', $targetFeed));
        }

        // Make sure we have photo details.
        if ($internalMetadata->getPhotoDetails() === null) {
            throw new \InvalidArgumentException('Photo details are missing from the internal metadata.');
        }

        try {
            // Upload photo file with one of our photo uploaders.
            if ($this->_useResumablePhotoUploader($targetFeed, $internalMetadata)) {
                $this->_uploadResumablePhoto($targetFeed, $internalMetadata);
            } else {
                $internalMetadata->setPhotoUploadResponse(
                    $this->_uploadPhotoInOnePiece($targetFeed, $internalMetadata)
                );
            }
        } catch (InstagramException $e) {
            // Pass Instagram's error as is.
            throw $e;
        } catch (\Exception $e) {
            // Wrap runtime errors.
            throw new UploadFailedException(
                sprintf(
                    'Upload of "%s" failed: %s',
                    $internalMetadata->getPhotoDetails()->getBasename(),
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Configures parameters for a *SINGLE* uploaded photo file.
     *
     * WARNING TO CONTRIBUTORS: THIS IS ONLY FOR *TIMELINE* AND *STORY* -PHOTOS-.
     * USE "configureTimelineAlbum()" FOR ALBUMS and "configureSingleVideo()" FOR VIDEOS.
     * AND IF FUTURE INSTAGRAM FEATURES NEED CONFIGURATION AND ARE NON-TRIVIAL,
     * GIVE THEM THEIR OWN FUNCTION LIKE WE DID WITH "configureTimelineAlbum()",
     * TO AVOID ADDING BUGGY AND UNMAINTAINABLE SPIDERWEB CODE!
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     * @param array            $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     */
    public function configureSinglePhoto(
        $targetFeed,
        InternalMetadata $internalMetadata,
        array $externalMetadata = [])
    {
        // Determine the target endpoint for the photo.
        switch ($targetFeed) {
        case Constants::FEED_TIMELINE:
            $endpoint = 'media/configure/';
            break;
        case Constants::FEED_DIRECT_STORY:
        case Constants::FEED_STORY:
            $endpoint = 'media/configure_to_story/';
            break;
        default:
            throw new \InvalidArgumentException(sprintf('Bad target feed "%s".', $targetFeed));
        }

        // Available external metadata parameters:
        /** @var string Caption to use for the media. */
        $captionText = isset($externalMetadata['caption']) ? $externalMetadata['caption'] : '';
        /** @var string Accesibility caption to use for the media. */
        $altText = isset($externalMetadata['custom_accessibility_caption']) ? $externalMetadata['custom_accessibility_caption'] : null;
        /** @var Response\Model\Location|null A Location object describing where
         * the media was taken. */
        $location = (isset($externalMetadata['location'])) ? $externalMetadata['location'] : null;
        /** @var array|null Array of story location sticker instructions. ONLY
         * USED FOR STORY MEDIA! */
        $locationSticker = (isset($externalMetadata['location_sticker']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['location_sticker'] : null;
        /** @var array|null Array of usertagging instructions, in the format
         * [['position'=>[0.5,0.5], 'user_id'=>'123'], ...]. ONLY FOR TIMELINE PHOTOS! */
        $usertags = (isset($externalMetadata['usertags']) && $targetFeed == Constants::FEED_TIMELINE) ? $externalMetadata['usertags'] : null;
        /** @var string|null Link to attach to the media. ONLY USED FOR STORY MEDIA,
         * AND YOU MUST HAVE A BUSINESS INSTAGRAM ACCOUNT TO POST A STORY LINK! */
        $link = (isset($externalMetadata['link']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['link'] : null;
        /** @var void Photo filter. THIS DOES NOTHING! All real filters are done in the mobile app. */
        // $filter = isset($externalMetadata['filter']) ? $externalMetadata['filter'] : null;
        $filter = null; // COMMENTED OUT SO USERS UNDERSTAND THEY CAN'T USE THIS!
        /** @var array Hashtags to use for the media. ONLY STORY MEDIA! */
        $hashtags = (isset($externalMetadata['hashtags']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['hashtags'] : null;
        /** @var array Mentions to use for the media. ONLY STORY MEDIA! */
        $storyMentions = (isset($externalMetadata['story_mentions']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_mentions'] : null;
        /** @var array Story poll to use for the media. ONLY STORY MEDIA! */
        $storyPoll = (isset($externalMetadata['story_polls']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_polls'] : null;
        /** @var array Story slider to use for the media. ONLY STORY MEDIA! */
        $storySlider = (isset($externalMetadata['story_sliders']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_sliders'] : null;
        /** @var array Story question to use for the media. ONLY STORY MEDIA */
        $storyQuestion = (isset($externalMetadata['story_questions']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_questions'] : null;
        /** @var array Story countdown to use for the media. ONLY STORY MEDIA */
        $storyCountdown = (isset($externalMetadata['story_countdowns']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_countdowns'] : null;
        /** @var array Attached media used to share media to story feed. ONLY STORY MEDIA! */
        $attachedMedia = (isset($externalMetadata['attached_media']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['attached_media'] : null;
        /** @var array Product Tags to use for the media. ONLY FOR TIMELINE PHOTOS! */
        $productTags = (isset($externalMetadata['product_tags']) && $targetFeed == Constants::FEED_TIMELINE) ? $externalMetadata['product_tags'] : null;

        // Fix very bad external user-metadata values.
        if (!is_string($captionText)) {
            $captionText = '';
        }

        // Critically important internal library-generated metadata parameters:
        /** @var string The ID of the entry to configure. */
        $uploadId = $internalMetadata->getUploadId();
        /** @var int Width of the photo. */
        $photoWidth = $internalMetadata->getPhotoDetails()->getWidth();
        /** @var int Height of the photo. */
        $photoHeight = $internalMetadata->getPhotoDetails()->getHeight();

        // Build the request...
        $request = $this->ig->request($endpoint)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('edits',
                [
                    'crop_original_size'    => [(float) $photoWidth, (float) $photoHeight],
                    'crop_zoom'             => 1.0,
                    'crop_center'           => [0.0, -0.0],
                ])
            ->addPost('device',
                [
                    'manufacturer'      => $this->ig->device->getManufacturer(),
                    'model'             => $this->ig->device->getModel(),
                    'android_version'   => $this->ig->device->getAndroidVersion(),
                    'android_release'   => $this->ig->device->getAndroidRelease(),
                ])
            ->addPost('extra',
                [
                    'source_width'  => $photoWidth,
                    'source_height' => $photoHeight,
                ]);

        switch ($targetFeed) {
            case Constants::FEED_TIMELINE:
                $date = date('Y:m:d H:i:s');
                $request
                    ->addParam('timezone_offset', date('Z'))
                    ->addPost('date_time_original', $date)
                    ->addPost('date_time_digitalized', $date)
                    ->addPost('caption', $captionText)
                    ->addPost('source_type', '4')
                    ->addPost('media_folder', 'Camera')
                    ->addPost('upload_id', $uploadId);

                if ($usertags !== null) {
                    Utils::throwIfInvalidUsertags($usertags);
                    $request->addPost('usertags', json_encode($usertags));
                }
                if ($productTags !== null) {
                    Utils::throwIfInvalidProductTags($productTags);
                    $request->addPost('product_tags', json_encode($productTags));
                }
                if ($altText !== null) {
                    $request->addPost('custom_accessibility_caption', $altText);
                }
                break;
            case Constants::FEED_STORY:
                if ($internalMetadata->isBestieMedia()) {
                    $request->addPost('audience', 'besties');
                }

                $request
                    ->addPost('client_shared_at', (string) time())
                    ->addPost('source_type', '3')
                    ->addPost('configure_mode', '1')
                    ->addPost('client_timestamp', (string) (time() - mt_rand(3, 10)))
                    ->addPost('upload_id', $uploadId);

                if (is_string($link) && Utils::hasValidWebURLSyntax($link)) {
                    $story_cta = '[{"links":[{"linkType": 1, "webUri":'.json_encode($link).', "androidClass": "", "package": "", "deeplinkUri": "", "callToActionTitle": "", "redirectUri": null, "leadGenFormId": "", "igUserId": "", "appInstallObjectiveInvalidationBehavior": null}]}]';
                    $request->addPost('story_cta', $story_cta);
                }
                if ($hashtags !== null && $captionText !== '') {
                    Utils::throwIfInvalidStoryHashtags($captionText, $hashtags);
                    $request
                        ->addPost('story_hashtags', json_encode($hashtags))
                        ->addPost('caption', $captionText)
                        ->addPost('mas_opt_in', 'NOT_PROMPTED');
                }
                if ($locationSticker !== null && $location !== null) {
                    Utils::throwIfInvalidStoryLocationSticker($locationSticker);
                    $request
                        ->addPost('story_locations', json_encode([$locationSticker]))
                        ->addPost('mas_opt_in', 'NOT_PROMPTED');
                }
                if ($storyMentions !== null && $captionText !== '') {
                    Utils::throwIfInvalidStoryMentions($storyMentions);
                    $request
                        ->addPost('reel_mentions', json_encode($storyMentions))
                        ->addPost('caption', str_replace(' ', '+', $captionText).'+')
                        ->addPost('mas_opt_in', 'NOT_PROMPTED');
                }
                if ($storyPoll !== null) {
                    Utils::throwIfInvalidStoryPoll($storyPoll);
                    $request
                        ->addPost('story_polls', json_encode($storyPoll))
                        ->addPost('internal_features', 'polling_sticker')
                        ->addPost('mas_opt_in', 'NOT_PROMPTED');
                }
                if ($storySlider !== null) {
                    Utils::throwIfInvalidStorySlider($storySlider);
                    $request
                        ->addPost('story_sliders', json_encode($storySlider))
                        ->addPost('story_sticker_ids', 'emoji_slider_'.$storySlider[0]['emoji']);
                }
                if ($storyQuestion !== null) {
                    Utils::throwIfInvalidStoryQuestion($storyQuestion);
                    $request
                        ->addPost('story_questions', json_encode($storyQuestion))
                        ->addPost('story_sticker_ids', 'question_sticker_ama');
                }
                if ($storyCountdown !== null) {
                    Utils::throwIfInvalidStoryCountdown($storyCountdown);
                    $request
                        ->addPost('story_countdowns', json_encode($storyCountdown))
                        ->addPost('story_sticker_ids', 'countdown_sticker_time');
                }
                if ($attachedMedia !== null) {
                    Utils::throwIfInvalidAttachedMedia($attachedMedia);
                    $request
                        ->addPost('attached_media', json_encode($attachedMedia))
                        ->addPost('story_sticker_ids', 'media_simple_'.reset($attachedMedia)['media_id']);
                }
                break;
            case Constants::FEED_DIRECT_STORY:
                $request
                    ->addPost('recipient_users', $internalMetadata->getDirectUsers())
                    ->addPost('thread_ids', $internalMetadata->getDirectThreads())
                    ->addPost('client_shared_at', (string) time())
                    ->addPost('source_type', '3')
                    ->addPost('configure_mode', '2')
                    ->addPost('client_timestamp', (string) (time() - mt_rand(3, 10)))
                    ->addPost('upload_id', $uploadId);
                break;
        }

        if ($location instanceof Response\Model\Location) {
            if ($targetFeed === Constants::FEED_TIMELINE) {
                $request->addPost('location', Utils::buildMediaLocationJSON($location));
            }
            if ($targetFeed === Constants::FEED_STORY && $locationSticker === null) {
                throw new \InvalidArgumentException('You must provide a location_sticker together with your story location.');
            }
            $request
                ->addPost('geotag_enabled', '1')
                ->addPost('posting_latitude', $location->getLat())
                ->addPost('posting_longitude', $location->getLng())
                ->addPost('media_latitude', $location->getLat())
                ->addPost('media_longitude', $location->getLng());
        }

        $configure = $request->getResponse(new Response\ConfigureResponse());

        return $configure;
    }

    /**
     * Uploads a raw video file.
     *
     * @param int                   $targetFeed       One of the FEED_X constants.
     * @param string                $videoFilename    The video filename.
     * @param InternalMetadata|null $internalMetadata (optional) Internal library-generated metadata object.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException If the video upload fails.
     *
     * @return InternalMetadata Updated internal metadata object.
     */
    public function uploadVideo(
        $targetFeed,
        $videoFilename,
        InternalMetadata $internalMetadata = null)
    {
        if ($internalMetadata === null) {
            $internalMetadata = new InternalMetadata();
        }

        try {
            if ($internalMetadata->getVideoDetails() === null) {
                $internalMetadata->setVideoDetails($targetFeed, $videoFilename);
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                sprintf('Failed to get photo details: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        try {
            if ($this->_useSegmentedVideoUploader($targetFeed, $internalMetadata)) {
                $this->_uploadSegmentedVideo($targetFeed, $internalMetadata);
            } elseif ($this->_useResumableVideoUploader($targetFeed, $internalMetadata)) {
                $this->_uploadResumableVideo($targetFeed, $internalMetadata);
            } else {
                // Request parameters for uploading a new video.
                $internalMetadata->setVideoUploadUrls($this->_requestVideoUploadURL($targetFeed, $internalMetadata));

                // Attempt to upload the video data.
                $internalMetadata->setVideoUploadResponse($this->_uploadVideoChunks($targetFeed, $internalMetadata));
            }
        } catch (InstagramException $e) {
            // Pass Instagram's error as is.
            throw $e;
        } catch (\Exception $e) {
            // Wrap runtime errors.
            throw new UploadFailedException(
                sprintf('Upload of "%s" failed: %s', basename($videoFilename), $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        return $internalMetadata;
    }

    /**
     * UPLOADS A *SINGLE* VIDEO.
     *
     * This is NOT used for albums!
     *
     * @param int                   $targetFeed       One of the FEED_X constants.
     * @param string                $videoFilename    The video filename.
     * @param InternalMetadata|null $internalMetadata (optional) Internal library-generated metadata object.
     * @param array                 $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException If the video upload fails.
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     *
     * @see Internal::configureSingleVideo() for available metadata fields.
     */
    public function uploadSingleVideo(
        $targetFeed,
        $videoFilename,
        InternalMetadata $internalMetadata = null,
        array $externalMetadata = [])
    {
        // Make sure we only allow these particular feeds for this function.
        if ($targetFeed !== Constants::FEED_TIMELINE
            && $targetFeed !== Constants::FEED_STORY
            && $targetFeed !== Constants::FEED_DIRECT_STORY
            && $targetFeed !== Constants::FEED_TV
        ) {
            throw new \InvalidArgumentException(sprintf('Bad target feed "%s".', $targetFeed));
        }

        // Attempt to upload the video.
        $internalMetadata = $this->uploadVideo($targetFeed, $videoFilename, $internalMetadata);

        // Attempt to upload the thumbnail, associated with our video's ID.
        $this->uploadVideoThumbnail($targetFeed, $internalMetadata, $externalMetadata);

        // Configure the uploaded video and attach it to our timeline/story.
        try {
            /** @var \InstagramAPI\Response\ConfigureResponse $configure */
            $configure = $this->ig->internal->configureWithRetries(
                function () use ($targetFeed, $internalMetadata, $externalMetadata) {
                    // Attempt to configure video parameters.
                    return $this->configureSingleVideo($targetFeed, $internalMetadata, $externalMetadata);
                }
            );
        } catch (InstagramException $e) {
            // Pass Instagram's error as is.
            throw $e;
        } catch (\Exception $e) {
            // Wrap runtime errors.
            throw new UploadFailedException(
                sprintf('Upload of "%s" failed: %s', basename($videoFilename), $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        return $configure;
    }

    /**
     * Performs a resumable upload of a photo file, with support for retries.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     * @param array            $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException
     */
    public function uploadVideoThumbnail(
        $targetFeed,
        InternalMetadata $internalMetadata,
        array $externalMetadata = [])
    {
        if ($internalMetadata->getVideoDetails() === null) {
            throw new \InvalidArgumentException('Video details are missing from the internal metadata.');
        }

        try {
            // Automatically crop&resize the thumbnail to Instagram's requirements.
            $options = ['targetFeed' => $targetFeed];
            if (isset($externalMetadata['thumbnail_timestamp'])) {
                $options['thumbnailTimestamp'] = $externalMetadata['thumbnail_timestamp'];
            }
            $videoThumbnail = new InstagramThumbnail(
                $internalMetadata->getVideoDetails()->getFilename(),
                $options
            );
            // Validate and upload the thumbnail.
            $internalMetadata->setPhotoDetails($targetFeed, $videoThumbnail->getFile());
            $this->uploadPhotoData($targetFeed, $internalMetadata);
        } catch (InstagramException $e) {
            // Pass Instagram's error as is.
            throw $e;
        } catch (\Exception $e) {
            // Wrap runtime errors.
            throw new UploadFailedException(
                sprintf('Upload of video thumbnail failed: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Asks Instagram for parameters for uploading a new video.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @throws \InstagramAPI\Exception\InstagramException If the request fails.
     *
     * @return \InstagramAPI\Response\UploadJobVideoResponse
     */
    protected function _requestVideoUploadURL(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        $request = $this->ig->request('upload/video/')
            ->setSignedPost(false)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid);

        foreach ($this->_getVideoUploadParams($targetFeed, $internalMetadata) as $key => $value) {
            $request->addPost($key, $value);
        }

        // Perform the "pre-upload" API request.
        /** @var Response\UploadJobVideoResponse $response */
        $response = $request->getResponse(new Response\UploadJobVideoResponse());

        return $response;
    }

    /**
     * Configures parameters for a *SINGLE* uploaded video file.
     *
     * WARNING TO CONTRIBUTORS: THIS IS ONLY FOR *TIMELINE* AND *STORY* -VIDEOS-.
     * USE "configureTimelineAlbum()" FOR ALBUMS and "configureSinglePhoto()" FOR PHOTOS.
     * AND IF FUTURE INSTAGRAM FEATURES NEED CONFIGURATION AND ARE NON-TRIVIAL,
     * GIVE THEM THEIR OWN FUNCTION LIKE WE DID WITH "configureTimelineAlbum()",
     * TO AVOID ADDING BUGGY AND UNMAINTAINABLE SPIDERWEB CODE!
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     * @param array            $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     */
    public function configureSingleVideo(
        $targetFeed,
        InternalMetadata $internalMetadata,
        array $externalMetadata = [])
    {
        // Determine the target endpoint for the video.
        switch ($targetFeed) {
        case Constants::FEED_TIMELINE:
            $endpoint = 'media/configure/';
            break;
        case Constants::FEED_DIRECT_STORY:
        case Constants::FEED_STORY:
            $endpoint = 'media/configure_to_story/';
            break;
        case Constants::FEED_TV:
            $endpoint = 'media/configure_to_igtv/';
            break;
        default:
            throw new \InvalidArgumentException(sprintf('Bad target feed "%s".', $targetFeed));
        }

        // Available external metadata parameters:
        /** @var string Caption to use for the media. */
        $captionText = isset($externalMetadata['caption']) ? $externalMetadata['caption'] : '';
        /** @var string[]|null Array of numerical UserPK IDs of people tagged in
         * your video. ONLY USED IN STORY VIDEOS! TODO: Actually, it's not even
         * implemented for stories. */
        $usertags = (isset($externalMetadata['usertags']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['usertags'] : null;
        /** @var Response\Model\Location|null A Location object describing where
         * the media was taken. */
        $location = (isset($externalMetadata['location'])) ? $externalMetadata['location'] : null;
        /** @var array|null Array of story location sticker instructions. ONLY
         * USED FOR STORY MEDIA! */
        $locationSticker = (isset($externalMetadata['location_sticker']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['location_sticker'] : null;
        /** @var string|null Link to attach to the media. ONLY USED FOR STORY MEDIA,
         * AND YOU MUST HAVE A BUSINESS INSTAGRAM ACCOUNT TO POST A STORY LINK! */
        $link = (isset($externalMetadata['link']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['link'] : null;
        /** @var array Hashtags to use for the media. ONLY STORY MEDIA! */
        $hashtags = (isset($externalMetadata['hashtags']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['hashtags'] : null;
        /** @var array Mentions to use for the media. ONLY STORY MEDIA! */
        $storyMentions = (isset($externalMetadata['story_mentions']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_mentions'] : null;
        /** @var array Story poll to use for the media. ONLY STORY MEDIA! */
        $storyPoll = (isset($externalMetadata['story_polls']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_polls'] : null;
        /** @var array Attached media used to share media to story feed. ONLY STORY MEDIA! */
        $storySlider = (isset($externalMetadata['story_sliders']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_sliders'] : null;
        /** @var array Story question to use for the media. ONLY STORY MEDIA */
        $storyQuestion = (isset($externalMetadata['story_questions']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_questions'] : null;
        /** @var array Story countdown to use for the media. ONLY STORY MEDIA */
        $storyCountdown = (isset($externalMetadata['story_countdowns']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['story_countdowns'] : null;
        /** @var array Attached media used to share media to story feed. ONLY STORY MEDIA! */
        $attachedMedia = (isset($externalMetadata['attached_media']) && $targetFeed == Constants::FEED_STORY) ? $externalMetadata['attached_media'] : null;
        /** @var array Title of the media uploaded to your channel. ONLY TV MEDIA! */
        $title = (isset($externalMetadata['title']) && $targetFeed == Constants::FEED_TV) ? $externalMetadata['title'] : null;
        /** @var bool Whether or not a preview should be posted to your feed. ONLY TV MEDIA! */
        $shareToFeed = (isset($externalMetadata['share_to_feed']) && $targetFeed == Constants::FEED_TV) ? $externalMetadata['share_to_feed'] : false;

        // Fix very bad external user-metadata values.
        if (!is_string($captionText)) {
            $captionText = '';
        }

        $uploadId = $internalMetadata->getUploadId();
        $videoDetails = $internalMetadata->getVideoDetails();

        // Build the request...
        $request = $this->ig->request($endpoint)
            ->addParam('video', 1)
            ->addPost('supported_capabilities_new', json_encode(Constants::SUPPORTED_CAPABILITIES))
            ->addPost('video_result', $internalMetadata->getVideoUploadResponse() !== null ? (string) $internalMetadata->getVideoUploadResponse()->getResult() : '')
            ->addPost('upload_id', $uploadId)
            ->addPost('poster_frame_index', 0)
            ->addPost('length', round($videoDetails->getDuration(), 1))
            ->addPost('audio_muted', $videoDetails->getAudioCodec() === null)
            ->addPost('filter_type', 0)
            ->addPost('source_type', 4)
            ->addPost('device',
                [
                    'manufacturer'      => $this->ig->device->getManufacturer(),
                    'model'             => $this->ig->device->getModel(),
                    'android_version'   => $this->ig->device->getAndroidVersion(),
                    'android_release'   => $this->ig->device->getAndroidRelease(),
                ])
            ->addPost('extra',
                [
                    'source_width'  => $videoDetails->getWidth(),
                    'source_height' => $videoDetails->getHeight(),
                ])
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id);

        switch ($targetFeed) {
            case Constants::FEED_TIMELINE:
                $request->addPost('caption', $captionText);
                break;
            case Constants::FEED_STORY:
                if ($internalMetadata->isBestieMedia()) {
                    $request->addPost('audience', 'besties');
                }

                $request
                    ->addPost('configure_mode', 1) // 1 - REEL_SHARE
                    ->addPost('story_media_creation_date', time() - mt_rand(10, 20))
                    ->addPost('client_shared_at', time() - mt_rand(3, 10))
                    ->addPost('client_timestamp', time());

                if (is_string($link) && Utils::hasValidWebURLSyntax($link)) {
                    $story_cta = '[{"links":[{"linkType": 1, "webUri":'.json_encode($link).', "androidClass": "", "package": "", "deeplinkUri": "", "callToActionTitle": "", "redirectUri": null, "leadGenFormId": "", "igUserId": "", "appInstallObjectiveInvalidationBehavior": null}]}]';
                    $request->addPost('story_cta', $story_cta);
                }
                if ($hashtags !== null && $captionText !== '') {
                    Utils::throwIfInvalidStoryHashtags($captionText, $hashtags);
                    $request
                        ->addPost('story_hashtags', json_encode($hashtags))
                        ->addPost('caption', $captionText)
                        ->addPost('mas_opt_in', 'NOT_PROMPTED');
                }
                if ($locationSticker !== null && $location !== null) {
                    Utils::throwIfInvalidStoryLocationSticker($locationSticker);
                    $request
                        ->addPost('story_locations', json_encode([$locationSticker]))
                        ->addPost('mas_opt_in', 'NOT_PROMPTED');
                }
                if ($storyMentions !== null && $captionText !== '') {
                    Utils::throwIfInvalidStoryMentions($storyMentions);
                    $request
                        ->addPost('reel_mentions', json_encode($storyMentions))
                        ->addPost('caption', str_replace(' ', '+', $captionText).'+')
                        ->addPost('mas_opt_in', 'NOT_PROMPTED');
                }
                if ($storyPoll !== null) {
                    Utils::throwIfInvalidStoryPoll($storyPoll);
                    $request
                        ->addPost('story_polls', json_encode($storyPoll))
                        ->addPost('internal_features', 'polling_sticker')
                        ->addPost('mas_opt_in', 'NOT_PROMPTED');
                }
                if ($storySlider !== null) {
                    Utils::throwIfInvalidStorySlider($storySlider);
                    $request
                        ->addPost('story_sliders', json_encode($storySlider))
                        ->addPost('story_sticker_ids', 'emoji_slider_'.$storySlider[0]['emoji']);
                }
                if ($storyQuestion !== null) {
                    Utils::throwIfInvalidStoryQuestion($storyQuestion);
                    $request
                        ->addPost('story_questions', json_encode($storyQuestion))
                        ->addPost('story_sticker_ids', 'question_sticker_ama');
                }
                if ($storyCountdown !== null) {
                    Utils::throwIfInvalidStoryCountdown($storyCountdown);
                    $request
                        ->addPost('story_countdowns', json_encode($storyCountdown))
                        ->addPost('story_sticker_ids', 'countdown_sticker_time');
                }
                if ($attachedMedia !== null) {
                    Utils::throwIfInvalidAttachedMedia($attachedMedia);
                    $request
                        ->addPost('attached_media', json_encode($attachedMedia))
                        ->addPost('story_sticker_ids', 'media_simple_'.reset($attachedMedia)['media_id']);
                }
                break;
            case Constants::FEED_DIRECT_STORY:
                $request
                    ->addPost('configure_mode', 2) // 2 - DIRECT_STORY_SHARE
                    ->addPost('recipient_users', $internalMetadata->getDirectUsers())
                    ->addPost('thread_ids', $internalMetadata->getDirectThreads())
                    ->addPost('story_media_creation_date', time() - mt_rand(10, 20))
                    ->addPost('client_shared_at', time() - mt_rand(3, 10))
                    ->addPost('client_timestamp', time());
                break;
            case Constants::FEED_TV:
                if ($title === null) {
                    throw new \InvalidArgumentException('You must provide a title for the media.');
                }
                if ($shareToFeed) {
                    if ($internalMetadata->getVideoDetails()->getDurationInMsec() < 60000) {
                        throw new \InvalidArgumentException('Your media must be at least a minute long to preview to feed.');
                    }
                    $request->addPost('igtv_share_preview_to_feed', '1');
                }
                $request
                    ->addPost('title', $title)
                    ->addPost('caption', $captionText);
                break;
        }

        if ($targetFeed == Constants::FEED_STORY) {
            $request->addPost('story_media_creation_date', time());
            if ($usertags !== null) {
                // Reel Mention example:
                // [{\"y\":0.3407772676161919,\"rotation\":0,\"user_id\":\"USER_ID\",\"x\":0.39892578125,\"width\":0.5619921875,\"height\":0.06011525487256372}]
                // NOTE: The backslashes are just double JSON encoding, ignore
                // that and just give us an array with these clean values, don't
                // try to encode it in any way, we do all encoding to match the above.
                // This post field will get wrapped in another json_encode call during transfer.
                $request->addPost('reel_mentions', json_encode($usertags));
            }
        }

        if ($location instanceof Response\Model\Location) {
            if ($targetFeed === Constants::FEED_TIMELINE) {
                $request->addPost('location', Utils::buildMediaLocationJSON($location));
            }
            if ($targetFeed === Constants::FEED_STORY && $locationSticker === null) {
                throw new \InvalidArgumentException('You must provide a location_sticker together with your story location.');
            }
            $request
                ->addPost('geotag_enabled', '1')
                ->addPost('posting_latitude', $location->getLat())
                ->addPost('posting_longitude', $location->getLng())
                ->addPost('media_latitude', $location->getLat())
                ->addPost('media_longitude', $location->getLng());
        }

        $configure = $request->getResponse(new Response\ConfigureResponse());

        return $configure;
    }

    /**
     * Configures parameters for a whole album of uploaded media files.
     *
     * WARNING TO CONTRIBUTORS: THIS IS ONLY FOR *TIMELINE ALBUMS*. DO NOT MAKE
     * IT DO ANYTHING ELSE, TO AVOID ADDING BUGGY AND UNMAINTAINABLE SPIDERWEB
     * CODE!
     *
     * @param array            $media            Extended media array coming from Timeline::uploadAlbum(),
     *                                           containing the user's per-file metadata,
     *                                           and internally generated per-file metadata.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object for the album itself.
     * @param array            $externalMetadata (optional) User-provided metadata key-value pairs
     *                                           for the album itself (its caption, location, etc).
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     */
    public function configureTimelineAlbum(
        array $media,
        InternalMetadata $internalMetadata,
        array $externalMetadata = [])
    {
        $endpoint = 'media/configure_sidecar/';

        $albumUploadId = $internalMetadata->getUploadId();

        // Available external metadata parameters:
        /** @var string Caption to use for the album. */
        $captionText = isset($externalMetadata['caption']) ? $externalMetadata['caption'] : '';
        /** @var Response\Model\Location|null A Location object describing where
         * the album was taken. */
        $location = isset($externalMetadata['location']) ? $externalMetadata['location'] : null;

        // Fix very bad external user-metadata values.
        if (!is_string($captionText)) {
            $captionText = '';
        }

        // Build the album's per-children metadata.
        $date = date('Y:m:d H:i:s');
        $childrenMetadata = [];
        foreach ($media as $item) {
            /** @var InternalMetadata $itemInternalMetadata */
            $itemInternalMetadata = $item['internalMetadata'];
            // Get all of the common, INTERNAL per-file metadata.
            $uploadId = $itemInternalMetadata->getUploadId();

            switch ($item['type']) {
            case 'photo':
                // Build this item's configuration.
                $photoConfig = [
                    'date_time_original'  => $date,
                    'scene_type'          => 1,
                    'disable_comments'    => false,
                    'upload_id'           => $uploadId,
                    'source_type'         => 0,
                    'scene_capture_type'  => 'standard',
                    'date_time_digitized' => $date,
                    'geotag_enabled'      => false,
                    'camera_position'     => 'back',
                    'edits'               => [
                        'filter_strength' => 1,
                        'filter_name'     => 'IGNormalFilter',
                    ],
                ];

                if (isset($item['usertags'])) {
                    // NOTE: These usertags were validated in Timeline::uploadAlbum.
                    $photoConfig['usertags'] = json_encode(['in' => $item['usertags']]);
                }

                $childrenMetadata[] = $photoConfig;
                break;
            case 'video':
                // Get all of the INTERNAL per-VIDEO metadata.
                $videoDetails = $itemInternalMetadata->getVideoDetails();

                // Build this item's configuration.
                $videoConfig = [
                    'length'              => round($videoDetails->getDuration(), 1),
                    'date_time_original'  => $date,
                    'scene_type'          => 1,
                    'poster_frame_index'  => 0,
                    'trim_type'           => 0,
                    'disable_comments'    => false,
                    'upload_id'           => $uploadId,
                    'source_type'         => 'library',
                    'geotag_enabled'      => false,
                    'edits'               => [
                        'length'          => round($videoDetails->getDuration(), 1),
                        'cinema'          => 'unsupported',
                        'original_length' => round($videoDetails->getDuration(), 1),
                        'source_type'     => 'library',
                        'start_time'      => 0,
                        'camera_position' => 'unknown',
                        'trim_type'       => 0,
                    ],
                ];

                if (isset($item['usertags'])) {
                    // NOTE: These usertags were validated in Timeline::uploadAlbum.
                    $videoConfig['usertags'] = json_encode(['in' => $item['usertags']]);
                }

                $childrenMetadata[] = $videoConfig;
                break;
            }
        }

        // Build the request...
        $request = $this->ig->request($endpoint)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('client_sidecar_id', $albumUploadId)
            ->addPost('caption', $captionText)
            ->addPost('children_metadata', $childrenMetadata);

        if ($location instanceof Response\Model\Location) {
            $request
                ->addPost('location', Utils::buildMediaLocationJSON($location))
                ->addPost('geotag_enabled', '1')
                ->addPost('posting_latitude', $location->getLat())
                ->addPost('posting_longitude', $location->getLng())
                ->addPost('media_latitude', $location->getLat())
                ->addPost('media_longitude', $location->getLng())
                ->addPost('exif_latitude', 0.0)
                ->addPost('exif_longitude', 0.0);
        }

        $configure = $request->getResponse(new Response\ConfigureResponse());

        return $configure;
    }

    /**
     * Saves active experiments.
     *
     * @param Response\SyncResponse $syncResponse
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    protected function _saveExperiments(
        Response\SyncResponse $syncResponse)
    {
        $experiments = [];
        foreach ($syncResponse->getExperiments() as $experiment) {
            $group = $experiment->getName();
            $params = $experiment->getParams();

            if ($group === null || $params === null) {
                continue;
            }

            if (!isset($experiments[$group])) {
                $experiments[$group] = [];
            }

            foreach ($params as $param) {
                $paramName = $param->getName();
                if ($paramName === null) {
                    continue;
                }

                $experiments[$group][$paramName] = $param->getValue();
            }
        }

        // Save the experiments and the last time we refreshed them.
        $this->ig->experiments = $this->ig->settings->setExperiments($experiments);
        $this->ig->settings->set('last_experiments', time());
    }

    /**
     * Perform an Instagram "feature synchronization" call for device.
     *
     * @param bool $prelogin
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SyncResponse
     */
    public function syncDeviceFeatures(
        $prelogin = false)
    {
        $request = $this->ig->request('qe/sync/')
            ->addHeader('X-DEVICE-ID', $this->ig->uuid)
            ->addPost('id', $this->ig->uuid)
            ->addPost('experiments', Constants::LOGIN_EXPERIMENTS);
        if ($prelogin) {
            $request->setNeedsAuth(false);
        } else {
            $request
                ->addPost('_uuid', $this->ig->uuid)
                ->addPost('_uid', $this->ig->account_id)
                ->addPost('_csrftoken', $this->ig->client->getToken());
        }

        return $request->getResponse(new Response\SyncResponse());
    }

    /**
     * Perform an Instagram "feature synchronization" call for account.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SyncResponse
     */
    public function syncUserFeatures()
    {
        $result = $this->ig->request('qe/sync/')
            ->addHeader('X-DEVICE-ID', $this->ig->uuid)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('id', $this->ig->account_id)
            ->addPost('experiments', Constants::EXPERIMENTS)
            ->getResponse(new Response\SyncResponse());

        // Save the updated experiments for this user.
        $this->_saveExperiments($result);

        return $result;
    }

    /**
     * Send launcher sync.
     *
     * @param bool $prelogin Indicates if the request is done before login request.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LauncherSyncResponse
     */
    public function sendLauncherSync(
        $prelogin)
    {
        $request = $this->ig->request('launcher/sync/')
            ->addPost('configs', Constants::LAUNCHER_CONFIGS);
        if ($prelogin) {
            $request
                ->setNeedsAuth(false)
                ->addPost('id', $this->ig->uuid);
        } else {
            $request
                ->addPost('id', $this->ig->account_id)
                ->addPost('_uuid', $this->ig->uuid)
                ->addPost('_uid', $this->ig->account_id)
                ->addPost('_csrftoken', $this->ig->client->getToken());
        }

        return $request->getResponse(new Response\LauncherSyncResponse());
    }

    /**
     * Registers advertising identifier.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function logAttribution()
    {
        return $this->ig->request('attribution/log_attribution/')
            ->setNeedsAuth(false)
            ->addPost('adid', $this->ig->advertising_id)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * TODO.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function logResurrectAttribution()
    {
        return $this->ig->request('attribution/log_resurrect_attribution/')
            ->addPost('adid', $this->ig->advertising_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Reads MSISDN header.
     *
     * @param string      $usage    Desired usage, either "ig_select_app" or "default".
     * @param string|null $subnoKey Encoded subscriber number.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\MsisdnHeaderResponse
     */
    public function readMsisdnHeader(
        $usage,
        $subnoKey = null)
    {
        $request = $this->ig->request('accounts/read_msisdn_header/')
            ->setNeedsAuth(false)
            ->addHeader('X-DEVICE-ID', $this->ig->uuid)
            // UUID is used as device_id intentionally.
            ->addPost('device_id', $this->ig->uuid)
            ->addPost('mobile_subno_usage', $usage);
        if ($subnoKey !== null) {
            $request->addPost('subno_key', $subnoKey);
        }

        return $request->getResponse(new Response\MsisdnHeaderResponse());
    }

    /**
     * Bootstraps MSISDN header.
     *
     * @param string $usage Mobile subno usage.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\MsisdnHeaderResponse
     *
     * @since 10.24.0 app version.
     */
    public function bootstrapMsisdnHeader(
        $usage = 'ig_select_app')
    {
        $request = $this->ig->request('accounts/msisdn_header_bootstrap/')
            ->setNeedsAuth(false)
            ->addPost('mobile_subno_usage', $usage)
            // UUID is used as device_id intentionally.
            ->addPost('device_id', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken());

        return $request->getResponse(new Response\MsisdnHeaderResponse());
    }

    /**
     * @param Response\Model\Token|null $token
     */
    protected function _saveZeroRatingToken(
        Response\Model\Token $token = null)
    {
        if ($token === null) {
            return;
        }

        $rules = [];
        foreach ($token->getRewriteRules() as $rule) {
            $rules[$rule->getMatcher()] = $rule->getReplacer();
        }
        $this->ig->client->zeroRating()->update($rules);

        try {
            $this->ig->settings->setRewriteRules($rules);
            $this->ig->settings->set('zr_token', $token->getTokenHash());
            $this->ig->settings->set('zr_expires', $token->expiresAt());
        } catch (SettingsException $e) {
            // Ignore storage errors.
        }
    }

    /**
     * Get zero rating token hash result.
     *
     * @param string $reason One of: "token_expired", "mqtt_token_push", "token_stale", "provisioning_time_mismatch".
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\TokenResultResponse
     */
    public function fetchZeroRatingToken(
        $reason = 'token_expired')
    {
        $request = $this->ig->request('zr/token/result/')
            ->setNeedsAuth(false)
            ->addParam('custom_device_id', $this->ig->uuid)
            ->addParam('device_id', $this->ig->device_id)
            ->addParam('fetch_reason', $reason)
            ->addParam('token_hash', (string) $this->ig->settings->get('zr_token'));

        /** @var Response\TokenResultResponse $result */
        $result = $request->getResponse(new Response\TokenResultResponse());
        $this->_saveZeroRatingToken($result->getToken());

        return $result;
    }

    /**
     * Get megaphone log.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\MegaphoneLogResponse
     */
    public function getMegaphoneLog()
    {
        return $this->ig->request('megaphone/log/')
            ->setSignedPost(false)
            ->addPost('type', 'feed_aysf')
            ->addPost('action', 'seen')
            ->addPost('reason', '')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('device_id', $this->ig->device_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('uuid', md5(time()))
            ->getResponse(new Response\MegaphoneLogResponse());
    }

    /**
     * Get hidden entities for users, places and hashtags via Facebook's algorithm.
     *
     * TODO: We don't know what this function does. If we ever discover that it
     * has a useful purpose, then we should move it somewhere else.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FacebookHiddenEntitiesResponse
     */
    public function getFacebookHiddenSearchEntities()
    {
        return $this->ig->request('fbsearch/get_hidden_search_entities/')
            ->getResponse(new Response\FacebookHiddenEntitiesResponse());
    }

    /**
     * Get Facebook OTA (Over-The-Air) update information.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FacebookOTAResponse
     */
    public function getFacebookOTA()
    {
        return $this->ig->request('facebook_ota/')
            ->addParam('fields', Constants::FACEBOOK_OTA_FIELDS)
            ->addParam('custom_user_id', $this->ig->account_id)
            ->addParam('signed_body', Signatures::generateSignature('').'.')
            ->addParam('ig_sig_key_version', Constants::SIG_KEY_VERSION)
            ->addParam('version_code', Constants::VERSION_CODE)
            ->addParam('version_name', Constants::IG_VERSION)
            ->addParam('custom_app_id', Constants::FACEBOOK_ORCA_APPLICATION_ID)
            ->addParam('custom_device_id', $this->ig->uuid)
            ->getResponse(new Response\FacebookOTAResponse());
    }

    /**
     * Fetch profiler traces config.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LoomFetchConfigResponse
     *
     * @see https://github.com/facebookincubator/profilo
     */
    public function getLoomFetchConfig()
    {
        return $this->ig->request('loom/fetch_config/')
            ->getResponse(new Response\LoomFetchConfigResponse());
    }

    /**
     * Get profile "notices".
     *
     * This is just for some internal state information, such as
     * "has_change_password_megaphone". It's not for public use.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ProfileNoticeResponse
     */
    public function getProfileNotice()
    {
        return $this->ig->request('users/profile_notice/')
            ->getResponse(new Response\ProfileNoticeResponse());
    }

    /**
     * Fetch quick promotions data.
     *
     * This is used by Instagram to fetch internal promotions or changes
     * about the platform. Latest quick promotion known was the new GDPR
     * policy where Instagram asks you to accept new policy and accept that
     * you have 18 years old or more.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FetchQPDataResponse
     */
    public function getQPFetch()
    {
        $query = 'viewer() {eligible_promotions.surface_nux_id(<surface>).external_gating_permitted_qps(<external_gating_permitted_qps>).supports_client_filters(true) {edges {priority,time_range {start,end},node {id,promotion_id,max_impressions,triggers,contextual_filters {clause_type,filters {filter_type,unknown_action,value {name,required,bool_value,int_value, string_value},extra_datas {name,required,bool_value,int_value, string_value}},clauses {clause_type,filters {filter_type,unknown_action,value {name,required,bool_value,int_value, string_value},extra_datas {name,required,bool_value,int_value, string_value}},clauses {clause_type,filters {filter_type,unknown_action,value {name,required,bool_value,int_value, string_value},extra_datas {name,required,bool_value,int_value, string_value}},clauses {clause_type,filters {filter_type,unknown_action,value {name,required,bool_value,int_value, string_value},extra_datas {name,required,bool_value,int_value, string_value}}}}}},template {name,parameters {name,required,bool_value,string_value,color_value,}},creatives {title {text},content {text},footer {text},social_context {text},primary_action{title {text},url,limit,dismiss_promotion},secondary_action{title {text},url,limit,dismiss_promotion},dismiss_action{title {text},url,limit,dismiss_promotion},image.scale(<scale>) {uri,width,height}}}}}}';

        return $this->ig->request('qp/batch_fetch/')
            ->addPost('vc_policy', 'default')
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('surfaces_to_queries', json_encode(
                [
                    Constants::SURFACE_PARAM[0] => $query,
                    Constants::SURFACE_PARAM[1] => $query,
                ]
            ))
            ->addPost('version', 1)
            ->addPost('scale', 2)
            ->getResponse(new Response\FetchQPDataResponse());
    }

    /**
     * Get quick promotions cooldowns.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\QPCooldownsResponse
     */
    public function getQPCooldowns()
    {
        return $this->ig->request('qp/get_cooldowns/')
            ->addParam('signed_body', Signatures::generateSignature(json_encode((object) []).'.{}'))
            ->addParam('ig_sig_key_version', Constants::SIG_KEY_VERSION)
            ->getResponse(new Response\QPCooldownsResponse());
    }

    /**
     * Internal helper for marking story media items as seen.
     *
     * This is used by story-related functions in other request-collections!
     *
     * @param Response\Model\Item[] $items    Array of one or more story media Items.
     * @param string|null           $sourceId Where the story was seen from,
     *                                        such as a location story-tray ID.
     *                                        If NULL, we automatically use the
     *                                        user's profile ID from each Item
     *                                        object as the source ID.
     * @param string                $module   Module where the story was found.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\MediaSeenResponse
     *
     * @see Story::markMediaSeen()
     * @see Location::markStoryMediaSeen()
     * @see Hashtag::markStoryMediaSeen()
     */
    public function markStoryMediaSeen(
        array $items,
        $sourceId = null,
        $module = 'feed_timeline')
    {
        // Build the list of seen media, with human randomization of seen-time.
        $reels = [];
        $maxSeenAt = time(); // Get current global UTC timestamp.
        $seenAt = $maxSeenAt - (3 * count($items)); // Start seenAt in the past.
        foreach ($items as $item) {
            if (!$item instanceof Response\Model\Item) {
                throw new \InvalidArgumentException(
                    'All story items must be instances of \InstagramAPI\Response\Model\Item.'
                );
            }

            // Raise "seenAt" if it's somehow older than the item's "takenAt".
            // NOTE: Can only happen if you see a story instantly when posted.
            $itemTakenAt = $item->getTakenAt();
            if ($seenAt < $itemTakenAt) {
                $seenAt = $itemTakenAt + 2;
            }

            // Do not let "seenAt" exceed the current global UTC time.
            if ($seenAt > $maxSeenAt) {
                $seenAt = $maxSeenAt;
            }

            // Determine the source ID for this item. This is where the item was
            // seen from, such as a UserID or a Location-StoryTray ID.
            $itemSourceId = ($sourceId === null ? $item->getUser()->getPk() : $sourceId);

            // Key Format: "mediaPk_userPk_sourceId".
            // NOTE: In case of seeing stories on a user's profile, their
            // userPk is used as the sourceId, as "mediaPk_userPk_userPk".
            $reelId = $item->getId().'_'.$itemSourceId;

            // Value Format: ["mediaTakenAt_seenAt"] (array with single string).
            $reels[$reelId] = [$itemTakenAt.'_'.$seenAt];

            // Randomly add 1-3 seconds to next seenAt timestamp, to act human.
            $seenAt += rand(1, 3);
        }

        return $this->ig->request('media/seen/')
            ->setVersion(2)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('container_module', $module)
            ->addPost('reels', $reels)
            ->addPost('reel_media_skipped', [])
            ->addPost('live_vods', [])
            ->addPost('live_vods_skipped', [])
            ->addPost('nuxes', [])
            ->addPost('nuxes_skipped', [])
            ->addParam('reel', 1)
            ->addParam('live_vod', 0)
            ->getResponse(new Response\MediaSeenResponse());
    }

    /**
     * Configure media entity (album, video, ...) with retries.
     *
     * @param callable $configurator Configurator function.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return Response
     */
    public function configureWithRetries(
        callable $configurator)
    {
        $attempt = 0;
        $lastError = null;
        while (true) {
            // Check for max retry-limit, and throw if we exceeded it.
            if (++$attempt > self::MAX_CONFIGURE_RETRIES) {
                if ($lastError === null) {
                    throw new \RuntimeException('All configuration retries have failed.');
                }

                throw new \RuntimeException(sprintf(
                    'All configuration retries have failed. Last error: %s',
                    $lastError
                ));
            }

            $result = null;

            try {
                /** @var Response $result */
                $result = $configurator();
            } catch (ThrottledException $e) {
                throw $e;
            } catch (LoginRequiredException $e) {
                throw $e;
            } catch (FeedbackRequiredException $e) {
                throw $e;
            } catch (ConsentRequiredException $e) {
                throw $e;
            } catch (CheckpointRequiredException $e) {
                throw $e;
            } catch (InstagramException $e) {
                if ($e->hasResponse()) {
                    $result = $e->getResponse();
                }
                $lastError = $e;
            } catch (\Exception $e) {
                $lastError = $e;
                // Ignore everything else.
            }

            // We had a network error or something like that, let's continue to the next attempt.
            if ($result === null) {
                sleep(1);
                continue;
            }

            $httpResponse = $result->getHttpResponse();
            $delay = 1;
            switch ($httpResponse->getStatusCode()) {
                case 200:
                    // Instagram uses "ok" status for this error, so we need to check it first:
                    // {"message": "media_needs_reupload", "error_title": "staged_position_not_found", "status": "ok"}
                    if (strtolower($result->getMessage()) === 'media_needs_reupload') {
                        throw new \RuntimeException(sprintf(
                            'You need to reupload the media (%s).',
                            // We are reading a property that isn't defined in the class
                            // property map, so we must use "has" first, to ensure it exists.
                            ($result->hasErrorTitle() && is_string($result->getErrorTitle())
                             ? $result->getErrorTitle()
                             : 'unknown error')
                        ));
                    } elseif ($result->isOk()) {
                        return $result;
                    }
                    // Continue to the next attempt.
                    break;
                case 202:
                    // We are reading a property that isn't defined in the class
                    // property map, so we must use "has" first, to ensure it exists.
                    if ($result->hasCooldownTimeInSeconds() && $result->getCooldownTimeInSeconds() !== null) {
                        $delay = max((int) $result->getCooldownTimeInSeconds(), 1);
                    }
                    break;
                default:
            }
            sleep($delay);
        }

        // We are never supposed to get here!
        throw new \LogicException('Something went wrong during configuration.');
    }

    /**
     * Performs a resumable upload of a media file, with support for retries.
     *
     * @param MediaDetails $mediaDetails
     * @param Request      $offsetTemplate
     * @param Request      $uploadTemplate
     * @param bool         $skipGet
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return Response\ResumableUploadResponse
     */
    protected function _uploadResumableMedia(
        MediaDetails $mediaDetails,
        Request $offsetTemplate,
        Request $uploadTemplate,
        $skipGet)
    {
        // Open file handle.
        $handle = fopen($mediaDetails->getFilename(), 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Failed to open media file for reading.');
        }

        try {
            $length = $mediaDetails->getFilesize();

            // Create a stream for the opened file handle.
            $stream = new Stream($handle, ['size' => $length]);

            $attempt = 0;
            while (true) {
                // Check for max retry-limit, and throw if we exceeded it.
                if (++$attempt > self::MAX_RESUMABLE_RETRIES) {
                    throw new \RuntimeException('All retries have failed.');
                }

                try {
                    if ($attempt === 1 && $skipGet) {
                        // It is obvious that the first attempt is always at 0, so we can skip a request.
                        $offset = 0;
                    } else {
                        // Get current offset.
                        $offsetRequest = clone $offsetTemplate;
                        /** @var Response\ResumableOffsetResponse $offsetResponse */
                        $offsetResponse = $offsetRequest->getResponse(new Response\ResumableOffsetResponse());
                        $offset = $offsetResponse->getOffset();
                    }

                    // Resume upload from given offset.
                    $uploadRequest = clone $uploadTemplate;
                    $uploadRequest
                        ->addHeader('Offset', $offset)
                        ->setBody(new LimitStream($stream, $length - $offset, $offset));
                    /** @var Response\ResumableUploadResponse $response */
                    $response = $uploadRequest->getResponse(new Response\ResumableUploadResponse());

                    return $response;
                } catch (ThrottledException $e) {
                    throw $e;
                } catch (LoginRequiredException $e) {
                    throw $e;
                } catch (FeedbackRequiredException $e) {
                    throw $e;
                } catch (ConsentRequiredException $e) {
                    throw $e;
                } catch (CheckpointRequiredException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    // Ignore everything else.
                }
            }
        } finally {
            Utils::safe_fclose($handle);
        }

        // We are never supposed to get here!
        throw new \LogicException('Something went wrong during media upload.');
    }

    /**
     * Performs an upload of a photo file, without support for retries.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UploadPhotoResponse
     */
    protected function _uploadPhotoInOnePiece(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        // Prepare payload for the upload request.
        $request = $this->ig->request('upload/photo/')
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addFile(
                'photo',
                $internalMetadata->getPhotoDetails()->getFilename(),
                'pending_media_'.Utils::generateUploadId().'.jpg'
            );

        foreach ($this->_getPhotoUploadParams($targetFeed, $internalMetadata) as $key => $value) {
            $request->addPost($key, $value);
        }
        /** @var Response\UploadPhotoResponse $response */
        $response = $request->getResponse(new Response\UploadPhotoResponse());

        return $response;
    }

    /**
     * Performs a resumable upload of a photo file, with support for retries.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    protected function _uploadResumablePhoto(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        $photoDetails = $internalMetadata->getPhotoDetails();

        $endpoint = sprintf('https://i.instagram.com/rupload_igphoto/%s_%d_%d',
            $internalMetadata->getUploadId(),
            0,
            Utils::hashCode($photoDetails->getFilename())
        );

        $uploadParams = $this->_getPhotoUploadParams($targetFeed, $internalMetadata);
        $uploadParams = Utils::reorderByHashCode($uploadParams);

        $offsetTemplate = new Request($this->ig, $endpoint);
        $offsetTemplate
            ->setAddDefaultHeaders(false)
            ->addHeader('X_FB_PHOTO_WATERFALL_ID', Signatures::generateUUID(true))
            ->addHeader('X-Instagram-Rupload-Params', json_encode($uploadParams));

        $uploadTemplate = clone $offsetTemplate;
        $uploadTemplate
            ->addHeader('X-Entity-Type', 'image/jpeg')
            ->addHeader('X-Entity-Name', basename(parse_url($endpoint, PHP_URL_PATH)))
            ->addHeader('X-Entity-Length', $photoDetails->getFilesize());

        return $this->_uploadResumableMedia(
            $photoDetails,
            $offsetTemplate,
            $uploadTemplate,
            $this->ig->isExperimentEnabled(
                'ig_android_skip_get_fbupload_photo_universe',
                'photo_skip_get'
            )
        );
    }

    /**
     * Determine whether to use resumable photo uploader based on target feed and internal metadata.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @return bool
     */
    protected function _useResumablePhotoUploader(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        switch ($targetFeed) {
            case Constants::FEED_TIMELINE_ALBUM:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_sidecar_photo_fbupload_universe',
                    'is_enabled_fbupload_sidecar_photo');
                break;
            default:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_photo_fbupload_universe',
                    'is_enabled_fbupload_photo');
        }

        return $result;
    }

    /**
     * Get the first missing range (start-end) from a HTTP "Range" header.
     *
     * @param string $ranges
     *
     * @return array|null
     */
    protected function _getFirstMissingRange(
        $ranges)
    {
        preg_match_all('/(?<start>\d+)-(?<end>\d+)\/(?<total>\d+)/', $ranges, $matches, PREG_SET_ORDER);
        if (!count($matches)) {
            return;
        }
        $pairs = [];
        $length = 0;
        foreach ($matches as $match) {
            $pairs[] = [$match['start'], $match['end']];
            $length = $match['total'];
        }
        // Sort pairs by start.
        usort($pairs, function (array $pair1, array $pair2) {
            return $pair1[0] - $pair2[0];
        });
        $first = $pairs[0];
        $second = count($pairs) > 1 ? $pairs[1] : null;
        if ($first[0] == 0) {
            $result = [$first[1] + 1, ($second === null ? $length : $second[0]) - 1];
        } else {
            $result = [0, $first[0] - 1];
        }

        return $result;
    }

    /**
     * Performs a chunked upload of a video file, with support for retries.
     *
     * Note that chunk uploads often get dropped when their server is overloaded
     * at peak hours, which is why our chunk-retry mechanism exists. We will
     * try several times to upload all chunks. The retries will only re-upload
     * the exact chunks that have been dropped from their server, and it won't
     * waste time with chunks that are already successfully uploaded.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UploadVideoResponse
     */
    protected function _uploadVideoChunks(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        $videoFilename = $internalMetadata->getVideoDetails()->getFilename();

        // To support video uploads to albums, we MUST fake-inject the
        // "sessionid" cookie from "i.instagram" into our "upload.instagram"
        // request, otherwise the server will reply with a "StagedUpload not
        // found" error when the final chunk has been uploaded.
        $sessionIDCookie = null;
        if ($targetFeed === Constants::FEED_TIMELINE_ALBUM) {
            $foundCookie = $this->ig->client->getCookie('sessionid', 'i.instagram.com');
            if ($foundCookie !== null) {
                $sessionIDCookie = $foundCookie->getValue();
            }
            if ($sessionIDCookie === null || $sessionIDCookie === '') { // Verify value.
                throw new \RuntimeException(
                    'Unable to find the necessary SessionID cookie for uploading video album chunks.'
                );
            }
        }

        // Verify the upload URLs.
        $uploadUrls = $internalMetadata->getVideoUploadUrls();
        if (!is_array($uploadUrls) || !count($uploadUrls)) {
            throw new \RuntimeException('No video upload URLs found.');
        }

        // Init state.
        $length = $internalMetadata->getVideoDetails()->getFilesize();
        $uploadId = $internalMetadata->getUploadId();
        $sessionId = sprintf('%s-%d', $uploadId, Utils::hashCode($videoFilename));
        $uploadUrl = array_shift($uploadUrls);
        $offset = 0;
        $chunk = min($length, self::MIN_CHUNK_SIZE);
        $attempt = 0;

        // Open file handle.
        $handle = fopen($videoFilename, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Failed to open file for reading.');
        }

        try {
            // Create a stream for the opened file handle.
            $stream = new Stream($handle);
            while (true) {
                // Check for this server's max retry-limit, and switch server?
                if (++$attempt > self::MAX_CHUNK_RETRIES) {
                    $uploadUrl = null;
                }

                // Try to switch to another server.
                if ($uploadUrl === null) {
                    $uploadUrl = array_shift($uploadUrls);
                    // Fail if there are no upload URLs left.
                    if ($uploadUrl === null) {
                        throw new \RuntimeException('There are no more upload URLs.');
                    }
                    // Reset state.
                    $attempt = 1; // As if "++$attempt" had ran once, above.
                    $offset = 0;
                    $chunk = min($length, self::MIN_CHUNK_SIZE);
                }

                // Prepare request.
                $request = new Request($this->ig, $uploadUrl->getUrl());
                $request
                    ->setAddDefaultHeaders(false)
                    ->addHeader('Content-Type', 'application/octet-stream')
                    ->addHeader('Session-ID', $sessionId)
                    ->addHeader('Content-Disposition', 'attachment; filename="video.mov"')
                    ->addHeader('Content-Range', 'bytes '.$offset.'-'.($offset + $chunk - 1).'/'.$length)
                    ->addHeader('job', $uploadUrl->getJob())
                    ->setBody(new LimitStream($stream, $chunk, $offset));

                // When uploading videos to albums, we must fake-inject the
                // "sessionid" cookie (the official app fake-injects it too).
                if ($targetFeed === Constants::FEED_TIMELINE_ALBUM && $sessionIDCookie !== null) {
                    // We'll add it with the default options ("single use")
                    // so the fake cookie is only added to THIS request.
                    $this->ig->client->fakeCookies()->add('sessionid', $sessionIDCookie);
                }

                // Perform the upload of the current chunk.
                $start = microtime(true);

                try {
                    $httpResponse = $request->getHttpResponse();
                } catch (NetworkException $e) {
                    // Ignore network exceptions.
                    continue;
                }

                // Determine new chunk size based on upload duration.
                $newChunkSize = (int) ($chunk / (microtime(true) - $start) * 5);
                // Ensure that the new chunk size is in valid range.
                $newChunkSize = min(self::MAX_CHUNK_SIZE, max(self::MIN_CHUNK_SIZE, $newChunkSize));

                $result = null;

                try {
                    /** @var Response\UploadVideoResponse $result */
                    $result = $request->getResponse(new Response\UploadVideoResponse());
                } catch (CheckpointRequiredException $e) {
                    throw $e;
                } catch (LoginRequiredException $e) {
                    throw $e;
                } catch (FeedbackRequiredException $e) {
                    throw $e;
                } catch (ConsentRequiredException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    // Ignore everything else.
                }

                // Process the server response...
                switch ($httpResponse->getStatusCode()) {
                    case 200:
                        // All chunks are uploaded, but if we don't have a
                        // response-result now then we must retry a new server.
                        if ($result === null) {
                            $uploadUrl = null;
                            break;
                        }

                        // SUCCESS! :-)
                        return $result;
                    case 201:
                        // The server has given us a regular reply. We expect it
                        // to be a range-reply, such as "0-3912399/23929393".
                        // Their server often drops chunks during peak hours,
                        // and in that case the first range may not start at
                        // zero, or there may be gaps or multiple ranges, such
                        // as "0-4076155/8152310,6114234-8152309/8152310". We'll
                        // handle that by re-uploading whatever they've dropped.
                        if (!$httpResponse->hasHeader('Range')) {
                            $uploadUrl = null;
                            break;
                        }
                        $range = $this->_getFirstMissingRange($httpResponse->getHeaderLine('Range'));
                        if ($range !== null) {
                            $offset = $range[0];
                            $chunk = min($newChunkSize, $range[1] - $range[0] + 1);
                        } else {
                            $chunk = min($newChunkSize, $length - $offset);
                        }

                        // Reset attempts count on successful upload.
                        $attempt = 0;
                        break;
                    case 400:
                    case 403:
                    case 511:
                        throw new \RuntimeException(sprintf(
                            'Instagram\'s server returned HTTP status "%d".',
                            $httpResponse->getStatusCode()
                        ));
                    case 422:
                        throw new \RuntimeException('Instagram\'s server says that the video is corrupt.');
                    default:
                }
            }
        } finally {
            // Guaranteed to release handle even if something bad happens above!
            Utils::safe_fclose($handle);
        }

        // We are never supposed to get here!
        throw new \LogicException('Something went wrong during video upload.');
    }

    /**
     * Performs a segmented upload of a video file, with support for retries.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    protected function _uploadSegmentedVideo(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        $videoDetails = $internalMetadata->getVideoDetails();

        // We must split the video into segments before running any requests.
        $segments = $this->_splitVideoIntoSegments($targetFeed, $videoDetails);

        $uploadParams = $this->_getVideoUploadParams($targetFeed, $internalMetadata);
        $uploadParams = Utils::reorderByHashCode($uploadParams);

        // This request gives us a stream identifier.
        $startRequest = new Request($this->ig, sprintf(
            'https://i.instagram.com/rupload_igvideo/%s?segmented=true&phase=start',
            Signatures::generateUUID()
        ));
        $startRequest
            ->setAddDefaultHeaders(false)
            ->addHeader('X-Instagram-Rupload-Params', json_encode($uploadParams))
            // Dirty hack to make a POST request.
            ->setBody(stream_for());
        /** @var Response\SegmentedStartResponse $startResponse */
        $startResponse = $startRequest->getResponse(new Response\SegmentedStartResponse());
        $streamId = $startResponse->getStreamId();

        // Upload the segments.
        try {
            $offset = 0;
            // Yep, no UUID here like in other resumable uploaders. Seems like a bug.
            $waterfallId = Utils::generateUploadId();
            foreach ($segments as $segment) {
                $endpoint = sprintf(
                    'https://i.instagram.com/rupload_igvideo/%s-%d-%d?segmented=true&phase=transfer',
                    md5($segment->getFilename()),
                    0,
                    $segment->getFilesize()
                );

                $offsetTemplate = new Request($this->ig, $endpoint);
                $offsetTemplate
                    ->setAddDefaultHeaders(false)
                    ->addHeader('Segment-Start-Offset', $offset)
                    // 1 => Audio, 2 => Video, 3 => Mixed.
                    ->addHeader('Segment-Type', $segment->getAudioCodec() !== null ? 1 : 2)
                    ->addHeader('Stream-Id', $streamId)
                    ->addHeader('X_FB_VIDEO_WATERFALL_ID', $waterfallId)
                    ->addHeader('X-Instagram-Rupload-Params', json_encode($uploadParams));

                $uploadTemplate = clone $offsetTemplate;
                $uploadTemplate
                    ->addHeader('X-Entity-Type', 'video/mp4')
                    ->addHeader('X-Entity-Name', basename(parse_url($endpoint, PHP_URL_PATH)))
                    ->addHeader('X-Entity-Length', $segment->getFilesize());

                $this->_uploadResumableMedia($segment, $offsetTemplate, $uploadTemplate, false);
                // Offset seems to be used just for ordering the segments.
                $offset += $segment->getFilesize();
            }
        } finally {
            // Remove the segments, because we don't need them anymore.
            foreach ($segments as $segment) {
                @unlink($segment->getFilename());
            }
        }

        // Finalize the upload.
        $endRequest = new Request($this->ig, sprintf(
            'https://i.instagram.com/rupload_igvideo/%s?segmented=true&phase=end',
            Signatures::generateUUID()
        ));
        $endRequest
            ->setAddDefaultHeaders(false)
            ->addHeader('Stream-Id', $streamId)
            ->addHeader('X-Instagram-Rupload-Params', json_encode($uploadParams))
            // Dirty hack to make a POST request.
            ->setBody(stream_for());
        /** @var Response\GenericResponse $result */
        $result = $endRequest->getResponse(new Response\GenericResponse());

        return $result;
    }

    /**
     * Performs a resumable upload of a video file, with support for retries.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    protected function _uploadResumableVideo(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        $rurCookie = $this->ig->client->getCookie('rur', 'i.instagram.com');
        if ($rurCookie === null || $rurCookie->getValue() === '') {
            throw new \RuntimeException(
                'Unable to find the necessary "rur" cookie for uploading video.'
            );
        }

        $videoDetails = $internalMetadata->getVideoDetails();

        $endpoint = sprintf('https://i.instagram.com/rupload_igvideo/%s_%d_%d?target=%s',
            $internalMetadata->getUploadId(),
            0,
            Utils::hashCode($videoDetails->getFilename()),
            $rurCookie->getValue()
        );

        $uploadParams = $this->_getVideoUploadParams($targetFeed, $internalMetadata);
        $uploadParams = Utils::reorderByHashCode($uploadParams);

        $offsetTemplate = new Request($this->ig, $endpoint);
        $offsetTemplate
            ->setAddDefaultHeaders(false)
            ->addHeader('X_FB_VIDEO_WATERFALL_ID', Signatures::generateUUID(true))
            ->addHeader('X-Instagram-Rupload-Params', json_encode($uploadParams));

        $uploadTemplate = clone $offsetTemplate;
        $uploadTemplate
            ->addHeader('X-Entity-Type', 'video/mp4')
            ->addHeader('X-Entity-Name', basename(parse_url($endpoint, PHP_URL_PATH)))
            ->addHeader('X-Entity-Length', $videoDetails->getFilesize());

        return $this->_uploadResumableMedia(
            $videoDetails,
            $offsetTemplate,
            $uploadTemplate,
            $this->ig->isExperimentEnabled(
                'ig_android_skip_get_fbupload_universe',
                'video_skip_get'
            )
        );
    }

    /**
     * Determine whether to use segmented video uploader based on target feed and internal metadata.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @return bool
     */
    protected function _useSegmentedVideoUploader(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        // No segmentation for album video.
        if ($targetFeed === Constants::FEED_TIMELINE_ALBUM) {
            return false;
        }

        // ffmpeg is required for video segmentation.
        try {
            FFmpeg::factory();
        } catch (\Exception $e) {
            return false;
        }

        // There is no need to segment short videos.
        switch ($targetFeed) {
            case Constants::FEED_TIMELINE:
                $minDuration = (int) $this->ig->getExperimentParam(
                    'ig_android_video_segmented_upload_universe',
                    // NOTE: This typo is intentional. Instagram named it that way.
                    'segment_duration_threashold_feed',
                    10
                );
                break;
            case Constants::FEED_STORY:
            case Constants::FEED_DIRECT_STORY:
                $minDuration = (int) $this->ig->getExperimentParam(
                    'ig_android_video_segmented_upload_universe',
                    // NOTE: This typo is intentional. Instagram named it that way.
                    'segment_duration_threashold_story_raven',
                    0
                );
                break;
            case Constants::FEED_TV:
                $minDuration = 150;
                break;
            default:
                $minDuration = 31536000; // 1 year.
        }
        if ((int) $internalMetadata->getVideoDetails()->getDuration() < $minDuration) {
            return false;
        }

        // Check experiments for the target feed.
        switch ($targetFeed) {
            case Constants::FEED_TIMELINE:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_video_segmented_upload_universe',
                    'segment_enabled_feed',
                    true);
                break;
            case Constants::FEED_DIRECT:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_direct_video_segmented_upload_universe',
                    'is_enabled_segment_direct');
                break;
            case Constants::FEED_STORY:
            case Constants::FEED_DIRECT_STORY:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_reel_raven_video_segmented_upload_universe',
                    'segment_enabled_story_raven');
                break;
            case Constants::FEED_TV:
                $result = true;
                break;
            default:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_video_segmented_upload_universe',
                    'segment_enabled_unknown',
                    true);
        }

        return $result;
    }

    /**
     * Determine whether to use resumable video uploader based on target feed and internal metadata.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @return bool
     */
    protected function _useResumableVideoUploader(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        switch ($targetFeed) {
            case Constants::FEED_TIMELINE_ALBUM:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_fbupload_sidecar_video_universe',
                    'is_enabled_fbupload_sidecar_video');
                break;
            case Constants::FEED_TIMELINE:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_upload_reliability_universe',
                    'is_enabled_fbupload_followers_share');
                break;
            case Constants::FEED_DIRECT:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_upload_reliability_universe',
                    'is_enabled_fbupload_direct_share');
                break;
            case Constants::FEED_STORY:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_upload_reliability_universe',
                    'is_enabled_fbupload_reel_share');
                break;
            case Constants::FEED_DIRECT_STORY:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_upload_reliability_universe',
                    'is_enabled_fbupload_story_share');
                break;
            case Constants::FEED_TV:
                $result = true;
                break;
            default:
                $result = $this->ig->isExperimentEnabled(
                    'ig_android_upload_reliability_universe',
                    'is_enabled_fbupload_unknown');
        }

        return $result;
    }

    /**
     * Get retry context for media upload.
     *
     * @return array
     */
    protected function _getRetryContext()
    {
        return [
            // TODO increment it with every fail.
            'num_step_auto_retry'   => 0,
            'num_reupload'          => 0,
            'num_step_manual_retry' => 0,
        ];
    }

    /**
     * Get params for photo upload job.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @return array
     */
    protected function _getPhotoUploadParams(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        // Common params.
        $result = [
            'upload_id'         => (string) $internalMetadata->getUploadId(),
            'retry_context'     => json_encode($this->_getRetryContext()),
            'image_compression' => '{"lib_name":"moz","lib_version":"3.1.m","quality":"87"}',
            'xsharing_user_ids' => json_encode([]),
            'media_type'        => $internalMetadata->getVideoDetails() !== null
                ? (string) Response\Model\Item::VIDEO
                : (string) Response\Model\Item::PHOTO,
        ];
        // Target feed's specific params.
        switch ($targetFeed) {
            case Constants::FEED_TIMELINE_ALBUM:
                $result['is_sidecar'] = '1';
                break;
            default:
        }

        return $result;
    }

    /**
     * Get params for video upload job.
     *
     * @param int              $targetFeed       One of the FEED_X constants.
     * @param InternalMetadata $internalMetadata Internal library-generated metadata object.
     *
     * @return array
     */
    protected function _getVideoUploadParams(
        $targetFeed,
        InternalMetadata $internalMetadata)
    {
        $videoDetails = $internalMetadata->getVideoDetails();
        // Common params.
        $result = [
            'upload_id'                => (string) $internalMetadata->getUploadId(),
            'retry_context'            => json_encode($this->_getRetryContext()),
            'xsharing_user_ids'        => json_encode([]),
            'upload_media_height'      => (string) $videoDetails->getHeight(),
            'upload_media_width'       => (string) $videoDetails->getWidth(),
            'upload_media_duration_ms' => (string) $videoDetails->getDurationInMsec(),
            'media_type'               => (string) Response\Model\Item::VIDEO,
            // TODO select with targetFeed (?)
            'potential_share_types'    => json_encode(['not supported type']),
        ];
        // Target feed's specific params.
        switch ($targetFeed) {
            case Constants::FEED_TIMELINE_ALBUM:
                $result['is_sidecar'] = '1';
                break;
            case Constants::FEED_DIRECT:
                $result['direct_v2'] = '1';
                $result['rotate'] = '0';
                $result['hflip'] = 'false';
                break;
            case Constants::FEED_STORY:
                $result['for_album'] = '1';
                break;
            case Constants::FEED_DIRECT_STORY:
                $result['for_direct_story'] = '1';
                break;
            case Constants::FEED_TV:
                $result['is_igtv_video'] = '1';
                break;
            default:
        }

        return $result;
    }

    /**
     * Find the segments after ffmpeg processing.
     *
     * @param string $outputDirectory The directory to look in.
     * @param string $prefix          The filename prefix.
     *
     * @return array
     */
    protected function _findSegments(
        $outputDirectory,
        $prefix)
    {
        // Video segments will be uploaded before the audio one.
        $result = glob("{$outputDirectory}/{$prefix}.video.*.mp4");

        // Audio always goes into one segment, so we can use is_file() here.
        $audioTrack = "{$outputDirectory}/{$prefix}.audio.mp4";
        if (is_file($audioTrack)) {
            $result[] = $audioTrack;
        }

        return $result;
    }

    /**
     * Split the video file into segments.
     *
     * @param int          $targetFeed      One of the FEED_X constants.
     * @param VideoDetails $videoDetails
     * @param FFmpeg|null  $ffmpeg
     * @param string|null  $outputDirectory
     *
     * @throws \Exception
     *
     * @return VideoDetails[]
     */
    protected function _splitVideoIntoSegments(
        $targetFeed,
        VideoDetails $videoDetails,
        FFmpeg $ffmpeg = null,
        $outputDirectory = null)
    {
        if ($ffmpeg === null) {
            $ffmpeg = FFmpeg::factory();
        }
        if ($outputDirectory === null) {
            $outputDirectory = Utils::$defaultTmpPath === null ? sys_get_temp_dir() : Utils::$defaultTmpPath;
        }
        // Check whether the output directory is valid.
        $targetDirectory = realpath($outputDirectory);
        if ($targetDirectory === false || !is_dir($targetDirectory) || !is_writable($targetDirectory)) {
            throw new \RuntimeException(sprintf(
                'Directory "%s" is missing or is not writable.',
                $outputDirectory
            ));
        }

        $prefix = sha1($videoDetails->getFilename().uniqid('', true));

        try {
            // Split the video stream into a multiple segments by time.
            $ffmpeg->run(sprintf(
                '-i %s -c:v copy -an -dn -sn -f segment -segment_time %d -segment_format mp4 %s',
                Args::escape($videoDetails->getFilename()),
                $this->_getTargetSegmentDuration($targetFeed),
                Args::escape(sprintf(
                    '%s%s%s.video.%%03d.mp4',
                    $outputDirectory,
                    DIRECTORY_SEPARATOR,
                    $prefix
                ))
            ));

            if ($videoDetails->getAudioCodec() !== null) {
                // Save the audio stream in one segment.
                $ffmpeg->run(sprintf(
                    '-i %s -c:a copy -vn -dn -sn -f mp4 %s',
                    Args::escape($videoDetails->getFilename()),
                    Args::escape(sprintf(
                        '%s%s%s.audio.mp4',
                        $outputDirectory,
                        DIRECTORY_SEPARATOR,
                        $prefix
                    ))
                ));
            }
        } catch (\RuntimeException $e) {
            // Find and remove all segments (if any).
            $files = $this->_findSegments($outputDirectory, $prefix);
            foreach ($files as $file) {
                @unlink($file);
            }
            // Re-throw the exception.
            throw $e;
        }

        // Collect segments.
        $files = $this->_findSegments($outputDirectory, $prefix);
        if (empty($files)) {
            throw new \RuntimeException('Something went wrong while splitting the video into segments.');
        }
        $result = [];

        try {
            // Wrap them into VideoDetails.
            foreach ($files as $file) {
                $result[] = new VideoDetails($file);
            }
        } catch (\Exception $e) {
            // Cleanup when something went wrong.
            foreach ($files as $file) {
                @unlink($file);
            }

            throw $e;
        }

        return $result;
    }

    /**
     * Get target segment duration in seconds.
     *
     * @param int $targetFeed One of the FEED_X constants.
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    protected function _getTargetSegmentDuration(
        $targetFeed)
    {
        switch ($targetFeed) {
            case Constants::FEED_TIMELINE:
                $duration = $this->ig->getExperimentParam(
                    'ig_android_video_segmented_upload_universe',
                    'target_segment_duration_feed',
                    5
                );
                break;
            case Constants::FEED_STORY:
            case Constants::FEED_DIRECT_STORY:
                $duration = $this->ig->getExperimentParam(
                    'ig_android_video_segmented_upload_universe',
                    'target_segment_duration_story_raven',
                    2
                );
                break;
            case Constants::FEED_TV:
                $duration = 100;
                break;
            default:
                throw new \InvalidArgumentException("Unsupported feed {$targetFeed}.");
        }

        return (int) $duration;
    }
}
