<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * PushPreferencesResponse.
 *
 * @method mixed getAds()
 * @method mixed getAnnouncements()
 * @method mixed getCommentLikes()
 * @method mixed getComments()
 * @method mixed getContactJoined()
 * @method mixed getDirectShareActivity()
 * @method mixed getFirstPost()
 * @method mixed getFollowRequestAccepted()
 * @method mixed getLikeAndCommentOnPhotoUserTagged()
 * @method mixed getLikes()
 * @method mixed getLiveBroadcast()
 * @method mixed getMessage()
 * @method mixed getNewFollower()
 * @method mixed getNotificationReminders()
 * @method mixed getPendingDirectShare()
 * @method Model\PushSettings[] getPushSettings()
 * @method mixed getReportUpdated()
 * @method string getStatus()
 * @method mixed getUserTagged()
 * @method mixed getViewCount()
 * @method Model\_Message[] get_Messages()
 * @method bool isAds()
 * @method bool isAnnouncements()
 * @method bool isCommentLikes()
 * @method bool isComments()
 * @method bool isContactJoined()
 * @method bool isDirectShareActivity()
 * @method bool isFirstPost()
 * @method bool isFollowRequestAccepted()
 * @method bool isLikeAndCommentOnPhotoUserTagged()
 * @method bool isLikes()
 * @method bool isLiveBroadcast()
 * @method bool isMessage()
 * @method bool isNewFollower()
 * @method bool isNotificationReminders()
 * @method bool isPendingDirectShare()
 * @method bool isPushSettings()
 * @method bool isReportUpdated()
 * @method bool isStatus()
 * @method bool isUserTagged()
 * @method bool isViewCount()
 * @method bool is_Messages()
 * @method $this setAds(mixed $value)
 * @method $this setAnnouncements(mixed $value)
 * @method $this setCommentLikes(mixed $value)
 * @method $this setComments(mixed $value)
 * @method $this setContactJoined(mixed $value)
 * @method $this setDirectShareActivity(mixed $value)
 * @method $this setFirstPost(mixed $value)
 * @method $this setFollowRequestAccepted(mixed $value)
 * @method $this setLikeAndCommentOnPhotoUserTagged(mixed $value)
 * @method $this setLikes(mixed $value)
 * @method $this setLiveBroadcast(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNewFollower(mixed $value)
 * @method $this setNotificationReminders(mixed $value)
 * @method $this setPendingDirectShare(mixed $value)
 * @method $this setPushSettings(Model\PushSettings[] $value)
 * @method $this setReportUpdated(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setUserTagged(mixed $value)
 * @method $this setViewCount(mixed $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAds()
 * @method $this unsetAnnouncements()
 * @method $this unsetCommentLikes()
 * @method $this unsetComments()
 * @method $this unsetContactJoined()
 * @method $this unsetDirectShareActivity()
 * @method $this unsetFirstPost()
 * @method $this unsetFollowRequestAccepted()
 * @method $this unsetLikeAndCommentOnPhotoUserTagged()
 * @method $this unsetLikes()
 * @method $this unsetLiveBroadcast()
 * @method $this unsetMessage()
 * @method $this unsetNewFollower()
 * @method $this unsetNotificationReminders()
 * @method $this unsetPendingDirectShare()
 * @method $this unsetPushSettings()
 * @method $this unsetReportUpdated()
 * @method $this unsetStatus()
 * @method $this unsetUserTagged()
 * @method $this unsetViewCount()
 * @method $this unset_Messages()
 */
class PushPreferencesResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'push_settings'                         => 'Model\PushSettings[]',
        'likes'                                 => '',
        'comments'                              => '',
        'comment_likes'                         => '',
        'like_and_comment_on_photo_user_tagged' => '',
        'live_broadcast'                        => '',
        'new_follower'                          => '',
        'follow_request_accepted'               => '',
        'contact_joined'                        => '',
        'pending_direct_share'                  => '',
        'direct_share_activity'                 => '',
        'user_tagged'                           => '',
        'notification_reminders'                => '',
        'first_post'                            => '',
        'announcements'                         => '',
        'ads'                                   => '',
        'view_count'                            => '',
        'report_updated'                        => '',
    ];
}
