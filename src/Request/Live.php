<?php

namespace InstagramAPI\Request;

use InstagramAPI\Response;
use InstagramAPI\Signatures;
use InstagramAPI\Utils;

/**
 * Functions for exploring and interacting with live broadcasts.
 */
class Live extends RequestCollection
{
    /**
     * Get suggested broadcasts.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SuggestedBroadcastsResponse
     */
    public function getSuggestedBroadcasts()
    {
        $endpoint = 'live/get_suggested_broadcasts/';
        if ($this->ig->isExperimentEnabled('ig_android_live_suggested_live_expansion', 'is_enabled')) {
            $endpoint = 'live/get_suggested_live_and_post_live/';
        }

        return $this->ig->request($endpoint)
            ->getResponse(new Response\SuggestedBroadcastsResponse());
    }

    /**
     * Get broadcast information.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastInfoResponse
     */
    public function getInfo(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/info/")
            ->getResponse(new Response\BroadcastInfoResponse());
    }

    /**
     * Get the viewer list of a broadcast.
     *
     * WARNING: You MUST be the owner of the broadcast. Otherwise Instagram won't send any API reply!
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ViewerListResponse
     */
    public function getViewerList(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/get_viewer_list/")
            ->getResponse(new Response\ViewerListResponse());
    }

    /**
     * Get the final viewer list of a broadcast after it has ended.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FinalViewerListResponse
     */
    public function getFinalViewerList(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/get_final_viewer_list/")
            ->getResponse(new Response\FinalViewerListResponse());
    }

    /**
     * Get the viewer list of a post-live (saved replay) broadcast.
     *
     * @param string      $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string|null $maxId       Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PostLiveViewerListResponse
     */
    public function getPostLiveViewerList(
        $broadcastId,
        $maxId = null)
    {
        $request = $this->ig->request("live/{$broadcastId}/get_post_live_viewers_list/");
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new Response\PostLiveViewerListResponse());
    }

    /**
     * Get a live broadcast's heartbeat and viewer count.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastHeartbeatAndViewerCountResponse
     */
    public function getHeartbeatAndViewerCount(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/heartbeat_and_get_viewer_count/")
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('offset_to_video_start', 0)
            ->getResponse(new Response\BroadcastHeartbeatAndViewerCountResponse());
    }

    /**
     * Get a live broadcast's join request counts.
     *
     * Note: This request **will** return null if there have been no pending
     * join requests have been made. Please have your code check for null.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $lastTotalCount Last join request count (optional).
     * @param int    $lastSeenTs     Last seen timestamp (optional).
     * @param int    $lastFetchTs    Last fetch timestamp (optional).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastJoinRequestCountResponse|null
     */
    public function getJoinRequestCounts(
        $broadcastId,
        $lastTotalCount = 0,
        $lastSeenTs = 0,
        $lastFetchTs = 0)
    {
        try {
            return $this->ig->request("live/{$broadcastId}/get_join_request_counts/")
                ->addParam('last_total_count', $lastTotalCount)
                ->addParam('last_seen_ts', $lastSeenTs)
                ->addParam('last_fetch_ts', $lastFetchTs)
                ->getResponse(new Response\BroadcastJoinRequestCountResponse());
        } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
            return null;
        }
    }

    /**
     * Show question in a live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $questionId  The question ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function showQuestion(
        $broadcastId,
        $questionId)
    {
        return $this->ig->request("live/{$broadcastId}/question/{$questionId}/activate/")
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Hide question in a live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $questionId  The question ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function hideQuestion(
        $broadcastId,
        $questionId)
    {
        return $this->ig->request("live/{$broadcastId}/question/{$questionId}/deactivate/")
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Asks a question to the host of the broadcast.
     *
     * Note: This function is only used by the viewers of a broadcast.
     *
     * @param string $broadcastId  The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $questionText Your question text.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function question(
        $broadcastId,
        $questionText)
    {
        return $this->ig->request("live/{$broadcastId}/questions")
            ->setSignedPost(false)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('text', $questionText)
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Get all received responses from a story question.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastQuestionsResponse
     */
    public function getQuestions()
    {
        return $this->ig->request('live/get_questions/')
            ->getResponse(new Response\BroadcastQuestionsResponse());
    }

    /**
     * Get all received responses from the current broadcast and a story question.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastQuestionsResponse
     */
    public function getLiveBroadcastQuestions(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/questions/")
            ->addParam('sources', 'story_and_live')
            ->getResponse(new Response\BroadcastQuestionsResponse());
    }

    /**
     * Acknowledges (waves at) a new user after they join.
     *
     * Note: This can only be done once to a user, per stream. Additionally, the user must have joined the stream.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $viewerId    Numerical UserPK ID of the user to wave to.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function wave(
        $broadcastId,
        $viewerId)
    {
        return $this->ig->request("live/{$broadcastId}/wave/")
            ->addPost('viewer_id', $viewerId)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Post a comment to a live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentText Your comment text.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CommentBroadcastResponse
     */
    public function comment(
        $broadcastId,
        $commentText)
    {
        return $this->ig->request("live/{$broadcastId}/comment/")
            ->addPost('user_breadcrumb', Utils::generateUserBreadcrumb(mb_strlen($commentText)))
            ->addPost('idempotence_token', Signatures::generateUUID(true))
            ->addPost('comment_text', $commentText)
            ->addPost('live_or_vod', 1)
            ->addPost('offset_to_video_start', 0)
            ->getResponse(new Response\CommentBroadcastResponse());
    }

    /**
     * Pin a comment on live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentId   Target comment ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PinCommentBroadcastResponse
     */
    public function pinComment(
        $broadcastId,
        $commentId)
    {
        return $this->ig->request("live/{$broadcastId}/pin_comment/")
            ->addPost('offset_to_video_start', 0)
            ->addPost('comment_id', $commentId)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\PinCommentBroadcastResponse());
    }

    /**
     * Unpin a comment on live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentId   Pinned comment ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UnpinCommentBroadcastResponse
     */
    public function unpinComment(
        $broadcastId,
        $commentId)
    {
        return $this->ig->request("live/{$broadcastId}/unpin_comment/")
            ->addPost('offset_to_video_start', 0)
            ->addPost('comment_id', $commentId)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\UnpinCommentBroadcastResponse());
    }

    /**
     * Get broadcast comments.
     *
     * @param string $broadcastId       The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $lastCommentTs     Last comments timestamp (optional).
     * @param int    $commentsRequested Number of comments requested (optional).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastCommentsResponse
     */
    public function getComments(
        $broadcastId,
        $lastCommentTs = 0,
        $commentsRequested = 3)
    {
        return $this->ig->request("live/{$broadcastId}/get_comment/")
            ->addParam('last_comment_ts', $lastCommentTs)
            ->addParam('num_comments_requested', $commentsRequested)
            ->getResponse(new Response\BroadcastCommentsResponse());
    }

    /**
     * Get post-live (saved replay) broadcast comments.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $startingOffset (optional) The time-offset to start at when retrieving the comments.
     * @param string $encodingTag    (optional) TODO: ?.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PostLiveCommentsResponse
     */
    public function getPostLiveComments(
        $broadcastId,
        $startingOffset = 0,
        $encodingTag = 'instagram_dash_remuxed')
    {
        return $this->ig->request("live/{$broadcastId}/get_post_live_comments/")
            ->addParam('starting_offset', $startingOffset)
            ->addParam('encoding_tag', $encodingTag)
            ->getResponse(new Response\PostLiveCommentsResponse());
    }

    /**
     * Enable viewer comments on your live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\EnableDisableLiveCommentsResponse
     */
    public function enableComments(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/unmute_comment/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\EnableDisableLiveCommentsResponse());
    }

    /**
     * Disable viewer comments on your live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\EnableDisableLiveCommentsResponse
     */
    public function disableComments(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/mute_comment/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\EnableDisableLiveCommentsResponse());
    }

    /**
     * Like a broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $likeCount   Number of likes ("hearts") to send (optional).
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastLikeResponse
     */
    public function like(
        $broadcastId,
        $likeCount = 1)
    {
        if ($likeCount < 1 || $likeCount > 6) {
            throw new \InvalidArgumentException('Like count must be a number from 1 to 6.');
        }

        return $this->ig->request("live/{$broadcastId}/like/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_like_count', $likeCount)
            ->getResponse(new Response\BroadcastLikeResponse());
    }

    /**
     * Get a live broadcast's like count.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $likeTs      Like timestamp.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastLikeCountResponse
     */
    public function getLikeCount(
        $broadcastId,
        $likeTs = 0)
    {
        return $this->ig->request("live/{$broadcastId}/get_like_count/")
            ->addParam('like_ts', $likeTs)
            ->getResponse(new Response\BroadcastLikeCountResponse());
    }

    /**
     * Get post-live (saved replay) broadcast likes.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $startingOffset (optional) The time-offset to start at when retrieving the likes.
     * @param string $encodingTag    (optional) TODO: ?.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PostLiveLikesResponse
     */
    public function getPostLiveLikes(
        $broadcastId,
        $startingOffset = 0,
        $encodingTag = 'instagram_dash_remuxed')
    {
        return $this->ig->request("live/{$broadcastId}/get_post_live_likes/")
            ->addParam('starting_offset', $startingOffset)
            ->addParam('encoding_tag', $encodingTag)
            ->getResponse(new Response\PostLiveLikesResponse());
    }

    /**
     * Create a live broadcast.
     *
     * Read the description of `start()` for proper usage.
     *
     * @param int $previewWidth  (optional) Width.
     * @param int $previewHeight (optional) Height.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CreateLiveResponse
     *
     * @see Live::start()
     * @see Live::end()
     */
    public function create(
        $previewWidth = 720,
        $previewHeight = 1184)
    {
        return $this->ig->request('live/create/')
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('preview_height', $previewHeight)
            ->addPost('preview_width', $previewWidth)
            ->addPost('broadcast_message', '')
            ->addPost('broadcast_type', 'RTMP_SWAP_ENABLED')
            ->addPost('internal_only', 0)
            ->getResponse(new Response\CreateLiveResponse());
    }

    /**
     * Start a live broadcast.
     *
     * Note that you MUST first call `create()` to get a broadcast-ID and its
     * RTMP upload-URL. Next, simply begin sending your actual video broadcast
     * to the stream-upload URL. And then call `start()` with the broadcast-ID
     * to make the stream available to viewers.
     *
     * Also note that broadcasting to the video stream URL must be done via
     * other software, since it ISN'T (and won't be) handled by this library!
     *
     * Lastly, note that stopping the stream is done either via RTMP signals,
     * which your broadcasting software MUST output properly (FFmpeg DOESN'T do
     * it without special patching!), OR by calling the `end()` function.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\StartLiveResponse
     *
     * @see Live::create()
     * @see Live::end()
     */
    public function start(
        $broadcastId)
    {
        $response = $this->ig->request("live/{$broadcastId}/start/")
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\StartLiveResponse());

        if ($this->ig->isExperimentEnabled('ig_android_live_qa_broadcaster_v1_universe', 'is_enabled')) {
            $this->ig->request("live/{$broadcastId}/question_status/")
                ->setSignedPost(false)
                ->addPost('_csrftoken', $this->ig->client->getToken())
                ->addPost('_uuid', $this->ig->uuid)
                ->addPost('allow_question_submission', true)
                ->getResponse(new Response\GenericResponse());
        }

        return $response;
    }

    /**
     * Acknowledges a copyright warning from Instagram after detected via a heartbeat request.
     *
     * `NOTE:` It is recommended that you view the `liveBroadcast` example
     * to see the proper usage of this function.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function resumeBroadcastAfterContentMatch(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/resume_broadcast_after_content_match/")
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * End a live broadcast.
     *
     * `NOTE:` To end your broadcast, you MUST use the `broadcast_id` value
     * which was assigned to you in the `create()` response.
     *
     * @param string $broadcastId      The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param bool   $copyrightWarning True when broadcast is ended via a copyright notice (optional).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     *
     * @see Live::create()
     * @see Live::start()
     */
    public function end(
        $broadcastId,
        $copyrightWarning = false)
    {
        return $this->ig->request("live/{$broadcastId}/end_broadcast/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('end_after_copyright_warning', $copyrightWarning)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Add a finished broadcast to your post-live feed (saved replay).
     *
     * The broadcast must have ended before you can call this function.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function addToPostLive(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/add_to_post_live/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Delete a saved post-live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function deletePostLive(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/delete_post_live/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }
}
