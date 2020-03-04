<?php

namespace InstagramAPI\Request;

use InstagramAPI\Constants;
use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Exception\UploadFailedException;
use InstagramAPI\Request\Metadata\Internal as InternalMetadata;
use InstagramAPI\Response;
use InstagramAPI\Utils;

/**
 * Functions for managing your timeline and interacting with other timelines.
 *
 * @see Media for more functions that let you interact with the media.
 * @see Usertag for functions that let you tag people in media.
 */
class Timeline extends RequestCollection
{
    /**
     * Uploads a photo to your Instagram timeline.
     *
     * @param string $photoFilename    The photo filename.
     * @param array  $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     *
     * @see Internal::configureSinglePhoto() for available metadata fields.
     */
    public function uploadPhoto(
        $photoFilename,
        array $externalMetadata = [])
    {
        return $this->ig->internal->uploadSinglePhoto(Constants::FEED_TIMELINE, $photoFilename, null, $externalMetadata);
    }

    /**
     * Uploads a video to your Instagram timeline.
     *
     * @param string $videoFilename    The video filename.
     * @param array  $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException If the video upload fails.
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     *
     * @see Internal::configureSingleVideo() for available metadata fields.
     */
    public function uploadVideo(
        $videoFilename,
        array $externalMetadata = [])
    {
        return $this->ig->internal->uploadSingleVideo(Constants::FEED_TIMELINE, $videoFilename, null, $externalMetadata);
    }

    /**
     * Uploads an album to your Instagram timeline.
     *
     * An album is also known as a "carousel" and "sidecar". They can contain up
     * to 10 photos or videos (at the moment).
     *
     * @param array $media            Array of image/video files and their per-file
     *                                metadata (type, file, and optionally
     *                                usertags). The "type" must be "photo" or
     *                                "video". The "file" must be its disk path.
     *                                And the optional "usertags" can only be
     *                                used on PHOTOS, never on videos.
     * @param array $externalMetadata (optional) User-provided metadata key-value pairs
     *                                for the album itself (its caption, location, etc).
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException If the video upload fails.
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     *
     * @see Internal::configureTimelineAlbum() for available album metadata fields.
     */
    public function uploadAlbum(
        array $media,
        array $externalMetadata = [])
    {
        if (empty($media)) {
            throw new \InvalidArgumentException("List of media to upload can't be empty.");
        }
        if (count($media) < 2 || count($media) > 10) {
            throw new \InvalidArgumentException(sprintf(
                'Instagram requires that albums contain 2-10 items. You tried to submit %d.',
                count($media)
            ));
        }

        // Figure out the media file details for ALL media in the album.
        // NOTE: We do this first, since it validates whether the media files are
        // valid and lets us avoid wasting time uploading totally invalid albums!
        foreach ($media as $key => $item) {
            if (!isset($item['file']) || !isset($item['type'])) {
                throw new \InvalidArgumentException(sprintf(
                    'Media at index "%s" does not have the required "file" and "type" keys.',
                    $key
                ));
            }

            $itemInternalMetadata = new InternalMetadata();

            // If usertags are provided, verify that the entries are valid.
            if (isset($item['usertags'])) {
                $item['usertags'] = ['in' => $item['usertags']];
                Utils::throwIfInvalidUsertags($item['usertags']);
            }

            // Pre-process media details and throw if not allowed on Instagram.
            switch ($item['type']) {
            case 'photo':
                // Determine the photo details.
                $itemInternalMetadata->setPhotoDetails(Constants::FEED_TIMELINE_ALBUM, $item['file']);
                break;
            case 'video':
                // Determine the video details.
                $itemInternalMetadata->setVideoDetails(Constants::FEED_TIMELINE_ALBUM, $item['file']);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unsupported album media type "%s".', $item['type']));
            }

            $media[$key]['internalMetadata'] = $itemInternalMetadata;
        }

        // Perform all media file uploads.
        foreach ($media as $key => $item) {
            /** @var InternalMetadata $itemInternalMetadata */
            $itemInternalMetadata = $media[$key]['internalMetadata'];

            switch ($item['type']) {
            case 'photo':
                $this->ig->internal->uploadPhotoData(Constants::FEED_TIMELINE_ALBUM, $itemInternalMetadata);
                break;
            case 'video':
                // Attempt to upload the video data.
                $itemInternalMetadata = $this->ig->internal->uploadVideo(Constants::FEED_TIMELINE_ALBUM, $item['file'], $itemInternalMetadata);

                // Attempt to upload the thumbnail, associated with our video's ID.
                $this->ig->internal->uploadVideoThumbnail(Constants::FEED_TIMELINE_ALBUM, $itemInternalMetadata, ['thumbnail_timestamp' => (isset($media[$key]['thumbnail_timestamp'])) ? $media[$key]['thumbnail_timestamp'] : 0]);
            }

            $media[$key]['internalMetadata'] = $itemInternalMetadata;
        }

        // Generate an uploadId (via internal metadata) for the album.
        $albumInternalMetadata = new InternalMetadata();
        // Configure the uploaded album and attach it to our timeline.
        try {
            /** @var \InstagramAPI\Response\ConfigureResponse $configure */
            $configure = $this->ig->internal->configureWithRetries(
                function () use ($media, $albumInternalMetadata, $externalMetadata) {
                    return $this->ig->internal->configureTimelineAlbum($media, $albumInternalMetadata, $externalMetadata);
                }
            );
        } catch (InstagramException $e) {
            // Pass Instagram's error as is.
            throw $e;
        } catch (\Exception $e) {
            // Wrap runtime errors.
            throw new UploadFailedException(
                sprintf('Upload of the album failed: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        return $configure;
    }

    /**
     * Get your "home screen" timeline feed.
     *
     * This is the feed of recent timeline posts from people you follow.
     *
     * @param string|null $maxId   Next "maximum ID", used for pagination.
     * @param array|null  $options An associative array with following keys (all
     *                             of them are optional):
     *                             "latest_story_pk" The media ID in Instagram's
     *                             internal format (ie "3482384834_43294");
     *                             "seen_posts" One or more seen media IDs;
     *                             "unseen_posts" One or more unseen media IDs;
     *                             "is_pull_to_refresh" Whether this call was
     *                             triggered by a refresh;
     *                             "push_disabled" Whether user has disabled
     *                             PUSH;
     *                             "recovered_from_crash" Whether the app has
     *                             recovered from a crash/was killed by Android
     *                             memory manager/force closed by user/just
     *                             installed for the first time;
     *                             "feed_view_info" DON'T USE IT YET.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\TimelineFeedResponse
     */
    public function getTimelineFeed(
        $maxId = null,
        array $options = null)
    {
        $asyncAds = $this->ig->isExperimentEnabled(
            'ig_android_ad_async_ads_universe',
            'is_enabled'
        );

        $request = $this->ig->request('feed/timeline/')
            ->setSignedPost(false)
            ->setIsBodyCompressed(true)
            //->addHeader('X-CM-Bandwidth-KBPS', '-1.000')
            //->addHeader('X-CM-Latency', '0.000')
            ->addHeader('X-Ads-Opt-Out', '0')
            ->addHeader('X-Google-AD-ID', $this->ig->advertising_id)
            ->addHeader('X-DEVICE-ID', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('is_prefetch', '0')
            ->addPost('phone_id', $this->ig->phone_id)
            ->addPost('device_id', $this->ig->uuid)
            ->addPost('client_session_id', $this->ig->session_id)
            ->addPost('battery_level', mt_rand(25, 100))
            ->addPost('is_charging', '0')
            ->addPost('will_sound_on', '1')
            ->addPost('is_on_screen', 'true')
            ->addPost('timezone_offset', date('Z'))
            ->addPost('is_async_ads_in_headload_enabled', (string) (int) ($asyncAds && $this->ig->isExperimentEnabled(
                    'ig_android_ad_async_ads_universe',
                    'is_async_ads_in_headload_enabled'
                )))
            ->addPost('is_async_ads_double_request', (string) (int) ($asyncAds && $this->ig->isExperimentEnabled(
                'ig_android_ad_async_ads_universe',
                'is_double_request_enabled'
            )))
            ->addPost('is_async_ads_rti', (string) (int) ($asyncAds && $this->ig->isExperimentEnabled(
                'ig_android_ad_async_ads_universe',
                'is_rti_enabled'
            )))
            ->addPost('rti_delivery_backend', (string) (int) $this->ig->getExperimentParam(
                'ig_android_ad_async_ads_universe',
                'rti_delivery_backend'
            ));

        if (isset($options['latest_story_pk'])) {
            $request->addPost('latest_story_pk', $options['latest_story_pk']);
        }

        if ($maxId !== null) {
            $request->addPost('reason', 'pagination');
            $request->addPost('max_id', $maxId);
            $request->addPost('is_pull_to_refresh', '0');
        } elseif (!empty($options['is_pull_to_refresh'])) {
            $request->addPost('reason', 'pull_to_refresh');
            $request->addPost('is_pull_to_refresh', '1');
        } elseif (isset($options['is_pull_to_refresh'])) {
            $request->addPost('reason', 'warm_start_fetch');
            $request->addPost('is_pull_to_refresh', '0');
        } else {
            $request->addPost('reason', 'cold_start_fetch');
            $request->addPost('is_pull_to_refresh', '0');
        }

        if (isset($options['seen_posts'])) {
            if (is_array($options['seen_posts'])) {
                $request->addPost('seen_posts', implode(',', $options['seen_posts']));
            } else {
                $request->addPost('seen_posts', $options['seen_posts']);
            }
        } elseif ($maxId === null) {
            $request->addPost('seen_posts', '');
        }

        if (isset($options['unseen_posts'])) {
            if (is_array($options['unseen_posts'])) {
                $request->addPost('unseen_posts', implode(',', $options['unseen_posts']));
            } else {
                $request->addPost('unseen_posts', $options['unseen_posts']);
            }
        } elseif ($maxId === null) {
            $request->addPost('unseen_posts', '');
        }

        if (isset($options['feed_view_info'])) {
            if (is_array($options['feed_view_info'])) {
                $request->addPost('feed_view_info', json_encode($options['feed_view_info']));
            } else {
                $request->addPost('feed_view_info', json_encode([$options['feed_view_info']]));
            }
        } elseif ($maxId === null) {
            $request->addPost('feed_view_info', '');
        }

        if (!empty($options['push_disabled'])) {
            $request->addPost('push_disabled', 'true');
        }

        if (!empty($options['recovered_from_crash'])) {
            $request->addPost('recovered_from_crash', '1');
        }

        return $request->getResponse(new Response\TimelineFeedResponse());
    }

    /**
     * Get a user's timeline feed.
     *
     * @param string      $userId Numerical UserPK ID.
     * @param string|null $maxId  Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UserFeedResponse
     */
    public function getUserFeed(
        $userId,
        $maxId = null)
    {
        $request = $this->ig->request("feed/user/{$userId}/")
            ->addParam('exclude_comment', true)
            ->addParam('only_fetch_first_carousel_media', false);

        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new Response\UserFeedResponse());
    }

    /**
     * Get your own timeline feed.
     *
     * @param string|null $maxId Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UserFeedResponse
     */
    public function getSelfUserFeed(
        $maxId = null)
    {
        return $this->getUserFeed($this->ig->account_id, $maxId);
    }

    /**
     * Get your archived timeline media feed.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UserFeedResponse
     */
    public function getArchivedMediaFeed()
    {
        return $this->ig->request('feed/only_me_feed/')
            ->getResponse(new Response\UserFeedResponse());
    }

    /**
     * Archives or unarchives one of your timeline media items.
     *
     * Marking media as "archived" will hide it from everyone except yourself.
     * You can unmark the media again at any time, to make it public again.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *                        "ALBUM", or the raw value of the Item's "getMediaType()" function.
     * @param bool   $onlyMe  If true, archives your media so that it's only visible to you.
     *                        Otherwise, if false, makes the media public to everyone again.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ArchiveMediaResponse
     */
    public function archiveMedia(
        $mediaId,
        $onlyMe = true)
    {
        $endpoint = $onlyMe ? 'only_me' : 'undo_only_me';

        return $this->ig->request("media/{$mediaId}/{$endpoint}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('media_id', $mediaId)
            ->getResponse(new Response\ArchiveMediaResponse());
    }

    /**
     * Backup all of your own uploaded photos and videos. :).
     *
     * Note that the backup filenames contain the date and time that the media
     * was uploaded. It uses PHP's timezone to calculate the local time. So be
     * sure to use date_default_timezone_set() with your local timezone if you
     * want correct times in the filenames!
     *
     * @param string $baseOutputPath (optional) Base-folder for output.
     *                               Uses "backups/" path in lib dir if null.
     * @param bool   $printProgress  (optional) Toggles terminal output.
     *
     * @throws \RuntimeException
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function backup(
        $baseOutputPath = null,
        $printProgress = true)
    {
        // Decide which path to use.
        if ($baseOutputPath === null) {
            $baseOutputPath = Constants::SRC_DIR.'/../backups/';
        }

        // Ensure that the whole directory path for the backup exists.
        $backupFolder = $baseOutputPath.$this->ig->username.'/'.date('Y-m-d').'/';
        if (!Utils::createFolder($backupFolder)) {
            throw new \RuntimeException(sprintf(
                'The "%s" backup folder is not writable.',
                $backupFolder
            ));
        }

        // Download all media to the output folders.
        $nextMaxId = null;
        do {
            $myTimeline = $this->getSelfUserFeed($nextMaxId);

            // Build a list of all media files on this page.
            $mediaFiles = []; // Reset queue.
            foreach ($myTimeline->getItems() as $item) {
                $itemDate = date('Y-m-d \a\t H.i.s O', $item->getTakenAt());
                if ($item->getMediaType() == Response\Model\Item::CAROUSEL) {
                    // Albums contain multiple items which must all be queued.
                    // NOTE: We won't name them by their subitem's getIds, since
                    // those Ids have no meaning outside of the album and they
                    // would just mean that the album content is spread out with
                    // wildly varying filenames. Instead, we will name all album
                    // items after their album's Id, with a position offset in
                    // their filename to show their position within the album.
                    $subPosition = 0;
                    foreach ($item->getCarouselMedia() as $subItem) {
                        ++$subPosition;
                        if ($subItem->getMediaType() == Response\Model\CarouselMedia::PHOTO) {
                            $mediaUrl = $subItem->getImageVersions2()->getCandidates()[0]->getUrl();
                        } else {
                            $mediaUrl = $subItem->getVideoVersions()[0]->getUrl();
                        }
                        $subItemId = sprintf('%s [%s-%02d]', $itemDate, $item->getId(), $subPosition);
                        $mediaFiles[$subItemId] = [
                            'taken_at' => $item->getTakenAt(),
                            'url'      => $mediaUrl,
                        ];
                    }
                } else {
                    if ($item->getMediaType() == Response\Model\Item::PHOTO) {
                        $mediaUrl = $item->getImageVersions2()->getCandidates()[0]->getUrl();
                    } else {
                        $mediaUrl = $item->getVideoVersions()[0]->getUrl();
                    }
                    $itemId = sprintf('%s [%s]', $itemDate, $item->getId());
                    $mediaFiles[$itemId] = [
                        'taken_at' => $item->getTakenAt(),
                        'url'      => $mediaUrl,
                    ];
                }
            }

            // Download all media files in the current page's file queue.
            foreach ($mediaFiles as $mediaId => $mediaInfo) {
                $mediaUrl = $mediaInfo['url'];
                $fileExtension = pathinfo(parse_url($mediaUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                $filePath = $backupFolder.$mediaId.'.'.$fileExtension;

                // Attempt to download the file.
                if ($printProgress) {
                    echo sprintf("* Downloading \"%s\" to \"%s\".\n", $mediaUrl, $filePath);
                }
                copy($mediaUrl, $filePath);

                // Set the file modification time to the taken_at timestamp.
                if (is_file($filePath)) {
                    touch($filePath, $mediaInfo['taken_at']);
                }
            }

            // Update the page ID to point to the next page (if more available).
            $nextMaxId = $myTimeline->getNextMaxId();
        } while ($nextMaxId !== null);
    }
}
