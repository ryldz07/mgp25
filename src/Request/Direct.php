<?php

namespace InstagramAPI\Request;

use InstagramAPI\Constants;
use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Exception\ThrottledException;
use InstagramAPI\Exception\UploadFailedException;
use InstagramAPI\Request\Metadata\Internal as InternalMetadata;
use InstagramAPI\Response;
use InstagramAPI\Signatures;
use InstagramAPI\Utils;

/**
 * Instagram Direct messaging functions.
 *
 * Be aware that many of the functions can take either a list of users or a
 * thread ID as their "recipient". If a thread already exists with those
 * user(s), you MUST use the "thread" recipient method (otherwise Instagram
 * rejects your bad API call). If no thread exists yet, you MUST use the
 * "users" recipient method a SINGLE time to create the thread first!
 */
class Direct extends RequestCollection
{
    /**
     * Get direct inbox messages for your account.
     *
     * @param string|null $cursorId Next "cursor ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectInboxResponse
     */
    public function getInbox(
        $cursorId = null)
    {
        $request = $this->ig->request('direct_v2/inbox/')
            ->addParam('persistentBadging', 'true')
            ->addParam('use_unified_inbox', 'true');
        if ($cursorId !== null) {
            $request->addParam('cursor', $cursorId);
        }

        return $request->getResponse(new Response\DirectInboxResponse());
    }

    /**
     * Get pending inbox data.
     *
     * @param string|null $cursorId Next "cursor ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectPendingInboxResponse
     */
    public function getPendingInbox(
        $cursorId = null)
    {
        $request = $this->ig->request('direct_v2/pending_inbox/')
            ->addParam('persistentBadging', 'true')
            ->addParam('use_unified_inbox', 'true');
        if ($cursorId !== null) {
            $request->addParam('cursor', $cursorId);
        }

        return $request->getResponse(new Response\DirectPendingInboxResponse());
    }

    /**
     * Approve pending threads by given identifiers.
     *
     * @param array $threads One or more thread identifiers.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function approvePendingThreads(
        array $threads)
    {
        if (!count($threads)) {
            throw new \InvalidArgumentException('Please provide at least one thread to approve.');
        }
        // Validate threads.
        foreach ($threads as &$thread) {
            if (!is_scalar($thread)) {
                throw new \InvalidArgumentException('Thread identifier must be scalar.');
            } elseif (!ctype_digit($thread) && (!is_int($thread) || $thread < 0)) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid thread identifier.', $thread));
            }
            $thread = (string) $thread;
        }
        unset($thread);
        // Choose appropriate endpoint.
        if (count($threads) > 1) {
            $request = $this->ig->request('direct_v2/threads/approve_multiple/')
                ->addPost('thread_ids', json_encode($threads));
        } else {
            /** @var string $thread */
            $thread = reset($threads);
            $request = $this->ig->request("direct_v2/threads/{$thread}/approve/");
        }

        return $request
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->setSignedPost(false)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Decline pending threads by given identifiers.
     *
     * @param array $threads One or more thread identifiers.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function declinePendingThreads(
        array $threads)
    {
        if (!count($threads)) {
            throw new \InvalidArgumentException('Please provide at least one thread to decline.');
        }
        // Validate threads.
        foreach ($threads as &$thread) {
            if (!is_scalar($thread)) {
                throw new \InvalidArgumentException('Thread identifier must be scalar.');
            } elseif (!ctype_digit($thread) && (!is_int($thread) || $thread < 0)) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid thread identifier.', $thread));
            }
            $thread = (string) $thread;
        }
        unset($thread);
        // Choose appropriate endpoint.
        if (count($threads) > 1) {
            $request = $this->ig->request('direct_v2/threads/decline_multiple/')
                ->addPost('thread_ids', json_encode($threads));
        } else {
            /** @var string $thread */
            $thread = reset($threads);
            $request = $this->ig->request("direct_v2/threads/{$thread}/decline/");
        }

        return $request
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->setSignedPost(false)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Decline all pending threads.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function declineAllPendingThreads()
    {
        return $this->ig->request('direct_v2/threads/decline_all/')
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->setSignedPost(false)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Get a list of activity statuses for users who you follow or message.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PresencesResponse
     */
    public function getPresences()
    {
        return $this->ig->request('direct_v2/get_presence/')
            ->getResponse(new Response\PresencesResponse());
    }

    /**
     * Get ranked list of recipients.
     *
     * WARNING: This is a special, very heavily throttled API endpoint.
     * Instagram REQUIRES that you wait several minutes between calls to it.
     *
     * @param string      $mode        Either "reshare" or "raven".
     * @param bool        $showThreads Whether to include existing threads into response.
     * @param string|null $query       (optional) The user to search for.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectRankedRecipientsResponse|null Will be NULL if throttled by Instagram.
     */
    public function getRankedRecipients(
        $mode,
        $showThreads,
        $query = null)
    {
        try {
            $request = $this->ig->request('direct_v2/ranked_recipients/')
                ->addParam('mode', $mode)
                ->addParam('show_threads', $showThreads ? 'true' : 'false')
                ->addParam('use_unified_inbox', 'true');
            if ($query !== null) {
                $request->addParam('query', $query);
            }

            return $request
                ->getResponse(new Response\DirectRankedRecipientsResponse());
        } catch (ThrottledException $e) {
            // Throttling is so common that we'll simply return NULL in that case.
            return null;
        }
    }

    /**
     * Get a thread by the recipients list.
     *
     * @param string[]|int[] $users Array of numerical UserPK IDs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectThreadResponse
     */
    public function getThreadByParticipants(
        array $users)
    {
        if (!count($users)) {
            throw new \InvalidArgumentException('Please provide at least one participant.');
        }
        foreach ($users as $user) {
            if (!is_scalar($user)) {
                throw new \InvalidArgumentException('User identifier must be scalar.');
            }
            if (!ctype_digit($user) && (!is_int($user) || $user < 0)) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid user identifier.', $user));
            }
        }
        $request = $this->ig->request('direct_v2/threads/get_by_participants/')
            ->addParam('recipient_users', '['.implode(',', $users).']');

        return $request->getResponse(new Response\DirectThreadResponse());
    }

    /**
     * Get direct message thread.
     *
     * @param string      $threadId Thread ID.
     * @param string|null $cursorId Next "cursor ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectThreadResponse
     */
    public function getThread(
        $threadId,
        $cursorId = null)
    {
        $request = $this->ig->request("direct_v2/threads/$threadId/")
            ->addParam('use_unified_inbox', 'true');
        if ($cursorId !== null) {
            $request->addParam('cursor', $cursorId);
        }

        return $request->getResponse(new Response\DirectThreadResponse());
    }

    /**
     * Get direct visual thread.
     *
     * `NOTE:` This "visual" endpoint is only used for Direct stories.
     *
     * @param string      $threadId Thread ID.
     * @param string|null $cursorId Next "cursor ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectVisualThreadResponse
     *
     * @deprecated Visual inbox has been superseded by the unified inbox.
     * @see Direct::getThread()
     */
    public function getVisualThread(
        $threadId,
        $cursorId = null)
    {
        $request = $this->ig->request("direct_v2/visual_threads/{$threadId}/");
        if ($cursorId !== null) {
            $request->addParam('cursor', $cursorId);
        }

        return $request->getResponse(new Response\DirectVisualThreadResponse());
    }

    /**
     * Update thread title.
     *
     * @param string $threadId Thread ID.
     * @param string $title    New title.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectThreadResponse
     */
    public function updateThreadTitle(
        $threadId,
        $title)
    {
        return $this->ig->request("direct_v2/threads/{$threadId}/update_title/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('title', trim($title))
            ->setSignedPost(false)
            ->getResponse(new Response\DirectThreadResponse());
    }

    /**
     * Mute direct thread.
     *
     * @param string $threadId Thread ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function muteThread(
        $threadId)
    {
        return $this->ig->request("direct_v2/threads/{$threadId}/mute/")
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->setSignedPost(false)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Unmute direct thread.
     *
     * @param string $threadId Thread ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function unmuteThread(
        $threadId)
    {
        return $this->ig->request("direct_v2/threads/{$threadId}/unmute/")
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->setSignedPost(false)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Create a private story sharing group.
     *
     * NOTE: In the official app, when you create a story, you can choose to
     * send it privately. And from there you can create a new group thread. So
     * this group creation endpoint is only meant to be used for "direct
     * stories" at the moment.
     *
     * @param string[]|int[] $userIds     Array of numerical UserPK IDs.
     * @param string         $threadTitle Name of the group thread.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectCreateGroupThreadResponse
     */
    public function createGroupThread(
        array $userIds,
        $threadTitle)
    {
        if (count($userIds) < 2) {
            throw new \InvalidArgumentException('You must invite at least 2 users to create a group.');
        }
        foreach ($userIds as &$user) {
            if (!is_scalar($user)) {
                throw new \InvalidArgumentException('User identifier must be scalar.');
            } elseif (!ctype_digit($user) && (!is_int($user) || $user < 0)) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid user identifier.', $user));
            }
            $user = (string) $user;
        }

        $request = $this->ig->request('direct_v2/create_group_thread/')
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('recipient_users', json_encode($userIds))
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('thread_title', $threadTitle);

        return $request->getResponse(new Response\DirectCreateGroupThreadResponse());
    }

    /**
     * Add users to thread.
     *
     * @param string         $threadId Thread ID.
     * @param string[]|int[] $users    Array of numerical UserPK IDs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectThreadResponse
     */
    public function addUsersToThread(
        $threadId,
        array $users)
    {
        if (!count($users)) {
            throw new \InvalidArgumentException('Please provide at least one user.');
        }
        foreach ($users as &$user) {
            if (!is_scalar($user)) {
                throw new \InvalidArgumentException('User identifier must be scalar.');
            } elseif (!ctype_digit($user) && (!is_int($user) || $user < 0)) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid user identifier.', $user));
            }
            $user = (string) $user;
        }

        return $this->ig->request("direct_v2/threads/{$threadId}/add_user/")
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_ids', json_encode($users))
            ->addPost('_uuid', $this->ig->uuid)
            ->setSignedPost(false)
            ->getResponse(new Response\DirectThreadResponse());
    }

    /**
     * Leave direct thread.
     *
     * @param string $threadId Thread ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function leaveThread(
        $threadId)
    {
        return $this->ig->request("direct_v2/threads/{$threadId}/leave/")
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->setSignedPost(false)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Hide direct thread.
     *
     * @param string $threadId Thread ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function hideThread(
        $threadId)
    {
        return $this->ig->request("direct_v2/threads/{$threadId}/hide/")
            ->addPost('use_unified_inbox', 'true')
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->setSignedPost(false)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Send a direct text message to a user's inbox.
     *
     * @param array  $recipients An array with "users" or "thread" keys.
     *                           To start a new thread, provide "users" as an array
     *                           of numerical UserPK IDs. To use an existing thread
     *                           instead, provide "thread" with the thread ID.
     * @param string $text       Text message.
     * @param array  $options    An associative array of optional parameters, including:
     *                           "client_context" - predefined UUID used to prevent double-posting.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    public function sendText(
        array $recipients,
        $text,
        array $options = [])
    {
        if (!strlen($text)) {
            throw new \InvalidArgumentException('Text can not be empty.');
        }

        $urls = Utils::extractURLs($text);
        if (count($urls)) {
            /** @var Response\DirectSendItemResponse $result */
            $result = $this->_sendDirectItem('links', $recipients, array_merge($options, [
                'link_urls' => json_encode(array_map(function (array $url) {
                    return $url['fullUrl'];
                }, $urls)),
                'link_text' => $text,
            ]));
        } else {
            /** @var Response\DirectSendItemResponse $result */
            $result = $this->_sendDirectItem('message', $recipients, array_merge($options, [
                'text' => $text,
            ]));
        }

        return $result;
    }

    /**
     * Share an existing media post via direct message to a user's inbox.
     *
     * @param array  $recipients An array with "users" or "thread" keys.
     *                           To start a new thread, provide "users" as an array
     *                           of numerical UserPK IDs. To use an existing thread
     *                           instead, provide "thread" with the thread ID.
     * @param string $mediaId    The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param array  $options    An associative array of additional parameters, including:
     *                           "media_type" (required) - either "photo" or "video";
     *                           "client_context" (optional) - predefined UUID used to prevent double-posting;
     *                           "text" (optional) - text message.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemsResponse
     *
     * @see https://help.instagram.com/1209246439090858 For more information.
     */
    public function sendPost(
        array $recipients,
        $mediaId,
        array $options = [])
    {
        if (!preg_match('#^\d+_\d+$#D', $mediaId)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid media ID.', $mediaId));
        }
        if (!isset($options['media_type'])) {
            throw new \InvalidArgumentException('Please provide media_type in options.');
        }
        if ($options['media_type'] !== 'photo' && $options['media_type'] !== 'video') {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid media_type.', $options['media_type']));
        }

        return $this->_sendDirectItems('media_share', $recipients, array_merge($options, [
            'media_id' => $mediaId,
        ]));
    }

    /**
     * Send a photo (upload) via direct message to a user's inbox.
     *
     * @param array  $recipients    An array with "users" or "thread" keys.
     *                              To start a new thread, provide "users" as an array
     *                              of numerical UserPK IDs. To use an existing thread
     *                              instead, provide "thread" with the thread ID.
     * @param string $photoFilename The photo filename.
     * @param array  $options       An associative array of optional parameters, including:
     *                              "client_context" - predefined UUID used to prevent double-posting.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    public function sendPhoto(
        array $recipients,
        $photoFilename,
        array $options = [])
    {
        if (!is_file($photoFilename) || !is_readable($photoFilename)) {
            throw new \InvalidArgumentException(sprintf('File "%s" is not available for reading.', $photoFilename));
        }

        return $this->_sendDirectItem('photo', $recipients, array_merge($options, [
            'filepath' => $photoFilename,
        ]));
    }

    /**
     * Send a disappearing photo (upload) via direct message to a user's inbox.
     *
     * @param array  $recipients       An array with "users" or "thread" keys.
     *                                 To start a new thread, provide "users" as an array
     *                                 of numerical UserPK IDs. To use an existing thread
     *                                 instead, provide "thread" with the thread ID.
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
    public function sendDisappearingPhoto(
        array $recipients,
        $photoFilename,
        array $externalMetadata = [])
    {
        $internalMetadata = new InternalMetadata();
        $internalMetadata->setDirectRecipients($this->_prepareRecipients($recipients, true));

        return $this->ig->internal->uploadSinglePhoto(Constants::FEED_DIRECT_STORY, $photoFilename, $internalMetadata, $externalMetadata);
    }

    /**
     * Send a video (upload) via direct message to a user's inbox.
     *
     * @param array  $recipients    An array with "users" or "thread" keys.
     *                              To start a new thread, provide "users" as an array
     *                              of numerical UserPK IDs. To use an existing thread
     *                              instead, provide "thread" with the thread ID.
     * @param string $videoFilename The video filename.
     * @param array  $options       An associative array of optional parameters, including:
     *                              "client_context" - predefined UUID used to prevent double-posting.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException If the video upload fails.
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    public function sendVideo(
        array $recipients,
        $videoFilename,
        array $options = [])
    {
        // Direct videos use different upload IDs.
        $internalMetadata = new InternalMetadata(Utils::generateUploadId(true));
        // Attempt to upload the video data.
        $internalMetadata = $this->ig->internal->uploadVideo(Constants::FEED_DIRECT, $videoFilename, $internalMetadata);

        // We must use the same client_context for all attempts to prevent double-posting.
        if (!isset($options['client_context'])) {
            $options['client_context'] = Signatures::generateUUID(true);
        }

        // Send the uploaded video to recipients.
        try {
            /** @var \InstagramAPI\Response\DirectSendItemResponse $result */
            $result = $this->ig->internal->configureWithRetries(
                function () use ($internalMetadata, $recipients, $options) {
                    $videoUploadResponse = $internalMetadata->getVideoUploadResponse();
                    // Attempt to configure video parameters (which sends it to the thread).
                    return $this->_sendDirectItem('video', $recipients, array_merge($options, [
                        'upload_id'    => $internalMetadata->getUploadId(),
                        'video_result' => $videoUploadResponse !== null ? $videoUploadResponse->getResult() : '',
                    ]));
                }
            );
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

        return $result;
    }

    /**
     * Send a disappearing video (upload) via direct message to a user's inbox.
     *
     * @param array  $recipients       An array with "users" or "thread" keys.
     *                                 To start a new thread, provide "users" as an array
     *                                 of numerical UserPK IDs. To use an existing thread
     *                                 instead, provide "thread" with the thread ID.
     * @param string $videoFilename    The video filename.
     * @param array  $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InstagramAPI\Exception\InstagramException
     * @throws \InstagramAPI\Exception\UploadFailedException If the video upload fails.
     *
     * @return \InstagramAPI\Response\ConfigureResponse
     *
     * @see Internal::configureSingleVideo() for available metadata fields.
     */
    public function sendDisappearingVideo(
        array $recipients,
        $videoFilename,
        array $externalMetadata = [])
    {
        $internalMetadata = new InternalMetadata();
        $internalMetadata->setDirectRecipients($this->_prepareRecipients($recipients, true));

        return $this->ig->internal->uploadSingleVideo(Constants::FEED_DIRECT_STORY, $videoFilename, $internalMetadata, $externalMetadata);
    }

    /**
     * Send a like to a user's inbox.
     *
     * @param array $recipients An array with "users" or "thread" keys.
     *                          To start a new thread, provide "users" as an array
     *                          of numerical UserPK IDs. To use an existing thread
     *                          instead, provide "thread" with the thread ID.
     * @param array $options    An associative array of optional parameters, including:
     *                          "client_context" - predefined UUID used to prevent double-posting.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    public function sendLike(
        array $recipients,
        array $options = [])
    {
        return $this->_sendDirectItem('like', $recipients, $options);
    }

    /**
     * Send a hashtag to a user's inbox.
     *
     * @param array  $recipients An array with "users" or "thread" keys.
     *                           To start a new thread, provide "users" as an array
     *                           of numerical UserPK IDs. To use an existing thread
     *                           instead, provide "thread" with the thread ID.
     * @param string $hashtag    Hashtag to share.
     * @param array  $options    An associative array of optional parameters, including:
     *                           "client_context" - predefined UUID used to prevent double-posting;
     *                           "text" - text message.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    public function sendHashtag(
        array $recipients,
        $hashtag,
        array $options = [])
    {
        if (!strlen($hashtag)) {
            throw new \InvalidArgumentException('Hashtag can not be empty.');
        }

        return $this->_sendDirectItem('hashtag', $recipients, array_merge($options, [
            'hashtag' => $hashtag,
        ]));
    }

    /**
     * Send a location to a user's inbox.
     *
     * You must provide a valid Instagram location ID, which you get via other
     * functions such as Location::search().
     *
     * @param array  $recipients An array with "users" or "thread" keys.
     *                           To start a new thread, provide "users" as an array
     *                           of numerical UserPK IDs. To use an existing thread
     *                           instead, provide "thread" with the thread ID.
     * @param string $locationId Instagram's internal ID for the location.
     * @param array  $options    An associative array of optional parameters, including:
     *                           "client_context" - predefined UUID used to prevent double-posting;
     *                           "text" - text message.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     *
     * @see Location::search()
     */
    public function sendLocation(
        array $recipients,
        $locationId,
        array $options = [])
    {
        if (!ctype_digit($locationId) && (!is_int($locationId) || $locationId < 0)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid location ID.', $locationId));
        }

        return $this->_sendDirectItem('location', $recipients, array_merge($options, [
            'venue_id' => $locationId,
        ]));
    }

    /**
     * Send a profile to a user's inbox.
     *
     * @param array  $recipients An array with "users" or "thread" keys.
     *                           To start a new thread, provide "users" as an array
     *                           of numerical UserPK IDs. To use an existing thread
     *                           instead, provide "thread" with the thread ID.
     * @param string $userId     Numerical UserPK ID.
     * @param array  $options    An associative array of optional parameters, including:
     *                           "client_context" - predefined UUID used to prevent double-posting;
     *                           "text" - text message.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    public function sendProfile(
        array $recipients,
        $userId,
        array $options = [])
    {
        if (!ctype_digit($userId) && (!is_int($userId) || $userId < 0)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid numerical UserPK ID.', $userId));
        }

        return $this->_sendDirectItem('profile', $recipients, array_merge($options, [
            'profile_user_id' => $userId,
        ]));
    }

    /**
     * Send a reaction to an existing thread item.
     *
     * @param string $threadId     Thread identifier.
     * @param string $threadItemId ThreadItemIdentifier.
     * @param string $reactionType One of: "like".
     * @param array  $options      An associative array of optional parameters, including:
     *                             "client_context" - predefined UUID used to prevent double-posting.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    public function sendReaction(
        $threadId,
        $threadItemId,
        $reactionType,
        array $options = [])
    {
        return $this->_handleReaction($threadId, $threadItemId, $reactionType, 'created', $options);
    }

    /**
     * Share an existing story post via direct message to a user's inbox.
     *
     * You are able to share your own stories, as well as public stories from
     * other people.
     *
     * @param array  $recipients An array with "users" or "thread" keys.
     *                           To start a new thread, provide "users" as an array
     *                           of numerical UserPK IDs. To use an existing thread
     *                           instead, provide "thread" with the thread ID.
     * @param string $storyId    The story ID in Instagram's internal format (ie "3482384834_43294").
     * @param string $reelId     The reel ID in Instagram's internal format (ie "highlight:12970012453081168")
     * @param array  $options    An associative array of additional parameters, including:
     *                           "media_type" (required) - either "photo" or "video";
     *                           "client_context" - predefined UUID used to prevent double-posting;
     *                           "text" - text message.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemsResponse
     *
     * @see https://help.instagram.com/188382041703187 For more information.
     */
    public function sendStory(
        array $recipients,
        $storyId,
        $reelId = null,
        array $options = [])
    {
        if (!preg_match('#^\d+_\d+$#D', $storyId)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid story ID.', $storyId));
        }
        if ($reelId !== null) {
            if (!preg_match('#^highlight:\d+$#D', $reelId)) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid reel ID.', $reelId));
            }
            $options = array_merge($options,
                [
                    'reel_id' => $reelId,
                ]);
        }
        if (!isset($options['media_type'])) {
            throw new \InvalidArgumentException('Please provide media_type in options.');
        }
        if ($options['media_type'] !== 'photo' && $options['media_type'] !== 'video') {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid media_type.', $options['media_type']));
        }

        return $this->_sendDirectItems('story_share', $recipients, array_merge($options, [
            'story_media_id' => $storyId,
        ]));
    }

    /**
     * Share an occurring or archived live stream via direct message to a user's inbox.
     *
     * You are able to share your own broadcasts, as well as broadcasts from
     * other people.
     *
     * @param array  $recipients  An array with "users" or "thread" keys.
     *                            To start a new thread, provide "users" as an array
     *                            of numerical UserPK IDs. To use an existing thread
     *                            instead, provide "thread" with the thread ID.
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param array  $options     An associative array of optional parameters, including:
     *                            "client_context" - predefined UUID used to prevent double-posting.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return Response\DirectSendItemResponse
     */
    public function sendLive(
        array $recipients,
        $broadcastId,
        array $options = [])
    {
        return $this->_sendDirectItem('live', $recipients, array_merge($options, [
            'broadcast_id' => $broadcastId,
        ]));
    }

    /**
     * Delete a reaction to an existing thread item.
     *
     * @param string $threadId     Thread identifier.
     * @param string $threadItemId ThreadItemIdentifier.
     * @param string $reactionType One of: "like".
     * @param array  $options      An associative array of optional parameters, including:
     *                             "client_context" - predefined UUID used to prevent double-posting.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    public function deleteReaction(
        $threadId,
        $threadItemId,
        $reactionType,
        array $options = [])
    {
        return $this->_handleReaction($threadId, $threadItemId, $reactionType, 'deleted', $options);
    }

    /**
     * Delete an item from given thread.
     *
     * @param string $threadId     Thread ID.
     * @param string $threadItemId Thread item ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function deleteItem(
        $threadId,
        $threadItemId)
    {
        return $this->ig->request("direct_v2/threads/{$threadId}/items/{$threadItemId}/delete/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->setSignedPost(false)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Marks an item from given thread as seen.
     *
     * @param string $threadId     Thread ID.
     * @param string $threadItemId Thread item ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSeenItemResponse
     */
    public function markItemSeen(
        $threadId,
        $threadItemId)
    {
        return $this->ig->request("direct_v2/threads/{$threadId}/items/{$threadItemId}/seen/")
            ->addPost('use_unified_inbox', 'true')
            ->addPost('action', 'mark_seen')
            ->addPost('thread_id', $threadId)
            ->addPost('item_id', $threadItemId)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->setSignedPost(false)
            ->getResponse(new Response\DirectSeenItemResponse());
    }

    /**
     * Marks visual items from given thread as seen.
     *
     * `NOTE:` This "visual" endpoint is only used for Direct stories.
     *
     * @param string          $threadId      Thread ID.
     * @param string|string[] $threadItemIds One or more thread item IDs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function markVisualItemsSeen(
        $threadId,
        $threadItemIds)
    {
        if (!is_array($threadItemIds)) {
            $threadItemIds = [$threadItemIds];
        } elseif (!count($threadItemIds)) {
            throw new \InvalidArgumentException('Please provide at least one thread item ID.');
        }

        return $this->ig->request("direct_v2/visual_threads/{$threadId}/item_seen/")
            ->addPost('item_ids', '['.implode(',', $threadItemIds).']')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Marks visual items from given thread as replayed.
     *
     * `NOTE:` This "visual" endpoint is only used for Direct stories.
     *
     * @param string          $threadId      Thread ID.
     * @param string|string[] $threadItemIds One or more thread item IDs.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function markVisualItemsReplayed(
        $threadId,
        $threadItemIds)
    {
        if (!is_array($threadItemIds)) {
            $threadItemIds = [$threadItemIds];
        } elseif (!count($threadItemIds)) {
            throw new \InvalidArgumentException('Please provide at least one thread item ID.');
        }

        return $this->ig->request("direct_v2/visual_threads/{$threadId}/item_replayed/")
            ->addPost('item_ids', '['.implode(',', $threadItemIds).']')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Validate and prepare recipients for direct messaging.
     *
     * @param array $recipients An array with "users" or "thread" keys.
     *                          To start a new thread, provide "users" as an array
     *                          of numerical UserPK IDs. To use an existing thread
     *                          instead, provide "thread" with the thread ID.
     * @param bool  $useQuotes  Whether to put IDs into quotes.
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function _prepareRecipients(
        array $recipients,
        $useQuotes)
    {
        $result = [];
        // users
        if (isset($recipients['users'])) {
            if (!is_array($recipients['users'])) {
                throw new \InvalidArgumentException('"users" must be an array.');
            }
            foreach ($recipients['users'] as $userId) {
                if (!is_scalar($userId)) {
                    throw new \InvalidArgumentException('User identifier must be scalar.');
                } elseif (!ctype_digit($userId) && (!is_int($userId) || $userId < 0)) {
                    throw new \InvalidArgumentException(sprintf('"%s" is not a valid user identifier.', $userId));
                }
            }
            // Although this is an array of groups, you will get "Only one group is supported." error
            // if you will try to use more than one group here.
            // We can't use json_encode() here, because each user id must be a number.
            $result['users'] = '[['.implode(',', $recipients['users']).']]';
        }
        // thread
        if (isset($recipients['thread'])) {
            if (!is_scalar($recipients['thread'])) {
                throw new \InvalidArgumentException('Thread identifier must be scalar.');
            } elseif (!ctype_digit($recipients['thread']) && (!is_int($recipients['thread']) || $recipients['thread'] < 0)) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid thread identifier.', $recipients['thread']));
            }
            // Although this is an array, you will get "Need to specify thread ID or recipient users." error
            // if you will try to use more than one thread identifier here.
            if (!$useQuotes) {
                // We can't use json_encode() here, because thread id must be a number.
                $result['thread'] = '['.$recipients['thread'].']';
            } else {
                // We can't use json_encode() here, because thread id must be a string.
                $result['thread'] = '["'.$recipients['thread'].'"]';
            }
        }
        if (!count($result)) {
            throw new \InvalidArgumentException('Please provide at least one recipient.');
        } elseif (isset($result['thread']) && isset($result['users'])) {
            throw new \InvalidArgumentException('You can not mix "users" with "thread".');
        }

        return $result;
    }

    /**
     * Send a direct message to specific users or thread.
     *
     * @param string $type       One of: "message", "like", "hashtag", "location", "profile", "photo",
     *                           "video", "links", "live".
     * @param array  $recipients An array with "users" or "thread" keys.
     *                           To start a new thread, provide "users" as an array
     *                           of numerical UserPK IDs. To use an existing thread
     *                           instead, provide "thread" with the thread ID.
     * @param array  $options    Depends on $type:
     *                           "message" uses "client_context" and "text";
     *                           "like" uses "client_context";
     *                           "hashtag" uses "client_context", "hashtag" and "text";
     *                           "location" uses "client_context", "venue_id" and "text";
     *                           "profile" uses "client_context", "profile_user_id" and "text";
     *                           "photo" uses "client_context" and "filepath";
     *                           "video" uses "client_context", "upload_id" and "video_result";
     *                           "links" uses "client_context", "link_text" and "link_urls".
     *                           "live" uses "client_context" and "text".
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    protected function _sendDirectItem(
        $type,
        array $recipients,
        array $options = [])
    {
        // Most requests are unsigned, but some use signing by overriding this.
        $signedPost = false;

        // Handle the request...
        switch ($type) {
            case 'message':
                $request = $this->ig->request('direct_v2/threads/broadcast/text/');
                // Check and set text.
                if (!isset($options['text'])) {
                    throw new \InvalidArgumentException('No text message provided.');
                }
                $request->addPost('text', $options['text']);
                break;
            case 'like':
                $request = $this->ig->request('direct_v2/threads/broadcast/like/');
                break;
            case 'hashtag':
                $request = $this->ig->request('direct_v2/threads/broadcast/hashtag/');
                // Check and set hashtag.
                if (!isset($options['hashtag'])) {
                    throw new \InvalidArgumentException('No hashtag provided.');
                }
                $request->addPost('hashtag', $options['hashtag']);
                // Set text if provided.
                if (isset($options['text']) && strlen($options['text'])) {
                    $request->addPost('text', $options['text']);
                }
                break;
            case 'location':
                $request = $this->ig->request('direct_v2/threads/broadcast/location/');
                // Check and set venue_id.
                if (!isset($options['venue_id'])) {
                    throw new \InvalidArgumentException('No venue_id provided.');
                }
                $request->addPost('venue_id', $options['venue_id']);
                // Set text if provided.
                if (isset($options['text']) && strlen($options['text'])) {
                    $request->addPost('text', $options['text']);
                }
                break;
            case 'profile':
                $request = $this->ig->request('direct_v2/threads/broadcast/profile/');
                // Check and set profile_user_id.
                if (!isset($options['profile_user_id'])) {
                    throw new \InvalidArgumentException('No profile_user_id provided.');
                }
                $request->addPost('profile_user_id', $options['profile_user_id']);
                // Set text if provided.
                if (isset($options['text']) && strlen($options['text'])) {
                    $request->addPost('text', $options['text']);
                }
                break;
            case 'photo':
                $request = $this->ig->request('direct_v2/threads/broadcast/upload_photo/');
                // Check and set filepath.
                if (!isset($options['filepath'])) {
                    throw new \InvalidArgumentException('No filepath provided.');
                }
                $request->addFile('photo', $options['filepath'], 'direct_temp_photo_'.Utils::generateUploadId().'.jpg');
                break;
            case 'video':
                $request = $this->ig->request('direct_v2/threads/broadcast/configure_video/');
                // Check and set upload_id.
                if (!isset($options['upload_id'])) {
                    throw new \InvalidArgumentException('No upload_id provided.');
                }
                $request->addPost('upload_id', $options['upload_id']);
                // Set video_result if provided.
                if (isset($options['video_result'])) {
                    $request->addPost('video_result', $options['video_result']);
                }
                break;
            case 'links':
                $request = $this->ig->request('direct_v2/threads/broadcast/link/');
                // Check and set link_urls.
                if (!isset($options['link_urls'])) {
                    throw new \InvalidArgumentException('No link_urls provided.');
                }
                $request->addPost('link_urls', $options['link_urls']);
                // Check and set link_text.
                if (!isset($options['link_text'])) {
                    throw new \InvalidArgumentException('No link_text provided.');
                }
                $request->addPost('link_text', $options['link_text']);
                break;
            case 'reaction':
                $request = $this->ig->request('direct_v2/threads/broadcast/reaction/');
                // Check and set reaction_type.
                if (!isset($options['reaction_type'])) {
                    throw new \InvalidArgumentException('No reaction_type provided.');
                }
                $request->addPost('reaction_type', $options['reaction_type']);
                // Check and set reaction_status.
                if (!isset($options['reaction_status'])) {
                    throw new \InvalidArgumentException('No reaction_status provided.');
                }
                $request->addPost('reaction_status', $options['reaction_status']);
                // Check and set item_id.
                if (!isset($options['item_id'])) {
                    throw new \InvalidArgumentException('No item_id provided.');
                }
                $request->addPost('item_id', $options['item_id']);
                // Check and set node_type.
                if (!isset($options['node_type'])) {
                    throw new \InvalidArgumentException('No node_type provided.');
                }
                $request->addPost('node_type', $options['node_type']);
                break;
            case 'live':
                $request = $this->ig->request('direct_v2/threads/broadcast/live_viewer_invite/');
                // Check and set broadcast id.
                if (!isset($options['broadcast_id'])) {
                    throw new \InvalidArgumentException('No broadcast_id provided.');
                }
                $request->addPost('broadcast_id', $options['broadcast_id']);
                // Set text if provided.
                if (isset($options['text']) && strlen($options['text'])) {
                    $request->addPost('text', $options['text']);
                }
                break;
            default:
                throw new \InvalidArgumentException('Unsupported _sendDirectItem() type.');
        }

        // Add recipients.
        $recipients = $this->_prepareRecipients($recipients, false);
        if (isset($recipients['users'])) {
            $request->addPost('recipient_users', $recipients['users']);
        } elseif (isset($recipients['thread'])) {
            $request->addPost('thread_ids', $recipients['thread']);
        } else {
            throw new \InvalidArgumentException('Please provide at least one recipient.');
        }

        // Handle client_context.
        if (!isset($options['client_context'])) {
            // WARNING: Must be random every time otherwise we can only
            // make a single post per direct-discussion thread.
            $options['client_context'] = Signatures::generateUUID(true);
        } elseif (!Signatures::isValidUUID($options['client_context'])) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid UUID.', $options['client_context']));
        }

        // Add some additional data if signed post.
        if ($signedPost) {
            $request->addPost('_uid', $this->ig->account_id);
        }

        // Execute the request with all data used by both signed and unsigned.
        return $request->setSignedPost($signedPost)
            ->addPost('action', 'send_item')
            ->addPost('client_context', $options['client_context'])
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\DirectSendItemResponse());
    }

    /**
     * Send a direct messages to specific users or thread.
     *
     * @param string $type       One of: "media_share", "story_share".
     * @param array  $recipients An array with "users" or "thread" keys.
     *                           To start a new thread, provide "users" as an array
     *                           of numerical UserPK IDs. To use an existing thread
     *                           instead, provide "thread" with the thread ID.
     * @param array  $options    Depends on $type:
     *                           "media_share" uses "client_context", "media_id", "media_type" and "text";
     *                           "story_share" uses "client_context", "story_media_id", "media_type" and "text".
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemsResponse
     */
    protected function _sendDirectItems(
        $type,
        array $recipients,
        array $options = [])
    {
        // Most requests are unsigned, but some use signing by overriding this.
        $signedPost = false;

        // Handle the request...
        switch ($type) {
            case 'media_share':
                $request = $this->ig->request('direct_v2/threads/broadcast/media_share/');
                // Check and set media_id.
                if (!isset($options['media_id'])) {
                    throw new \InvalidArgumentException('No media_id provided.');
                }
                $request->addPost('media_id', $options['media_id']);
                // Set text if provided.
                if (isset($options['text']) && strlen($options['text'])) {
                    $request->addPost('text', $options['text']);
                }
                // Check and set media_type.
                if (isset($options['media_type']) && $options['media_type'] === 'video') {
                    $request->addParam('media_type', 'video');
                } else {
                    $request->addParam('media_type', 'photo');
                }
                break;
            case 'story_share':
                $signedPost = true; // This must be a signed post!
                $request = $this->ig->request('direct_v2/threads/broadcast/story_share/');
                // Check and set story_media_id.
                if (!isset($options['story_media_id'])) {
                    throw new \InvalidArgumentException('No story_media_id provided.');
                }
                $request->addPost('story_media_id', $options['story_media_id']);
                // Set text if provided.
                if (isset($options['reel_id'])) {
                    $request->addPost('reel_id', $options['reel_id']);
                }
                // Set text if provided.
                if (isset($options['text']) && strlen($options['text'])) {
                    $request->addPost('text', $options['text']);
                }
                // Check and set media_type.
                if (isset($options['media_type']) && $options['media_type'] === 'video') {
                    $request->addParam('media_type', 'video');
                } else {
                    $request->addParam('media_type', 'photo');
                }
                break;
            default:
                throw new \InvalidArgumentException('Unsupported _sendDirectItems() type.');
        }

        // Add recipients.
        $recipients = $this->_prepareRecipients($recipients, false);
        if (isset($recipients['users'])) {
            $request->addPost('recipient_users', $recipients['users']);
        } elseif (isset($recipients['thread'])) {
            $request->addPost('thread_ids', $recipients['thread']);
        } else {
            throw new \InvalidArgumentException('Please provide at least one recipient.');
        }

        // Handle client_context.
        if (!isset($options['client_context'])) {
            // WARNING: Must be random every time otherwise we can only
            // make a single post per direct-discussion thread.
            $options['client_context'] = Signatures::generateUUID(true);
        } elseif (!Signatures::isValidUUID($options['client_context'])) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid UUID.', $options['client_context']));
        }

        // Add some additional data if signed post.
        if ($signedPost) {
            $request->addPost('_uid', $this->ig->account_id);
        }

        // Execute the request with all data used by both signed and unsigned.
        return $request->setSignedPost($signedPost)
            ->addPost('action', 'send_item')
            ->addPost('unified_broadcast_format', '1')
            ->addPost('client_context', $options['client_context'])
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\DirectSendItemsResponse());
    }

    /**
     * Handle a reaction to an existing thread item.
     *
     * @param string $threadId       Thread identifier.
     * @param string $threadItemId   ThreadItemIdentifier.
     * @param string $reactionType   One of: "like".
     * @param string $reactionStatus One of: "created", "deleted".
     * @param array  $options        An associative array of optional parameters, including:
     *                               "client_context" - predefined UUID used to prevent double-posting.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DirectSendItemResponse
     */
    protected function _handleReaction(
        $threadId,
        $threadItemId,
        $reactionType,
        $reactionStatus,
        array $options = [])
    {
        if (!ctype_digit($threadId) && (!is_int($threadId) || $threadId < 0)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid thread ID.', $threadId));
        }
        if (!ctype_digit($threadItemId) && (!is_int($threadItemId) || $threadItemId < 0)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid thread item ID.', $threadItemId));
        }
        if (!in_array($reactionType, ['like'], true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a supported reaction type.', $reactionType));
        }

        return $this->_sendDirectItem('reaction', ['thread' => $threadId], array_merge($options, [
            'reaction_type'   => $reactionType,
            'reaction_status' => $reactionStatus,
            'item_id'         => $threadItemId,
            'node_type'       => 'item',
        ]));
    }
}
