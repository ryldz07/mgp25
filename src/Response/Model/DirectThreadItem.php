<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectThreadItem.
 *
 * @method ActionLog getActionLog()
 * @method AnimatedMedia getAnimatedMedia()
 * @method string getClientContext()
 * @method MediaShare getDirectMediaShare()
 * @method DirectExpiringSummary getExpiringMediaActionSummary()
 * @method FelixShare getFelixShare()
 * @method mixed getHideInThread()
 * @method string getItemId()
 * @method mixed getItemType()
 * @method mixed getLike()
 * @method DirectLink getLink()
 * @method LiveVideoShare getLiveVideoShare()
 * @method LiveViewerInvite getLiveViewerInvite()
 * @method Location getLocation()
 * @method DirectThreadItemMedia getMedia()
 * @method Item getMediaShare()
 * @method Placeholder getPlaceholder()
 * @method Item[] getPreviewMedias()
 * @method ProductShare getProductShare()
 * @method User getProfile()
 * @method Item getRavenMedia()
 * @method DirectReactions getReactions()
 * @method ReelShare getReelShare()
 * @method string[] getSeenUserIds()
 * @method StoryShare getStoryShare()
 * @method string getText()
 * @method mixed getTimestamp()
 * @method string getUserId()
 * @method VideoCallEvent getVideoCallEvent()
 * @method VoiceMedia getVoiceMedia()
 * @method bool isActionLog()
 * @method bool isAnimatedMedia()
 * @method bool isClientContext()
 * @method bool isDirectMediaShare()
 * @method bool isExpiringMediaActionSummary()
 * @method bool isFelixShare()
 * @method bool isHideInThread()
 * @method bool isItemId()
 * @method bool isItemType()
 * @method bool isLike()
 * @method bool isLink()
 * @method bool isLiveVideoShare()
 * @method bool isLiveViewerInvite()
 * @method bool isLocation()
 * @method bool isMedia()
 * @method bool isMediaShare()
 * @method bool isPlaceholder()
 * @method bool isPreviewMedias()
 * @method bool isProductShare()
 * @method bool isProfile()
 * @method bool isRavenMedia()
 * @method bool isReactions()
 * @method bool isReelShare()
 * @method bool isSeenUserIds()
 * @method bool isStoryShare()
 * @method bool isText()
 * @method bool isTimestamp()
 * @method bool isUserId()
 * @method bool isVideoCallEvent()
 * @method bool isVoiceMedia()
 * @method $this setActionLog(ActionLog $value)
 * @method $this setAnimatedMedia(AnimatedMedia $value)
 * @method $this setClientContext(string $value)
 * @method $this setDirectMediaShare(MediaShare $value)
 * @method $this setExpiringMediaActionSummary(DirectExpiringSummary $value)
 * @method $this setFelixShare(FelixShare $value)
 * @method $this setHideInThread(mixed $value)
 * @method $this setItemId(string $value)
 * @method $this setItemType(mixed $value)
 * @method $this setLike(mixed $value)
 * @method $this setLink(DirectLink $value)
 * @method $this setLiveVideoShare(LiveVideoShare $value)
 * @method $this setLiveViewerInvite(LiveViewerInvite $value)
 * @method $this setLocation(Location $value)
 * @method $this setMedia(DirectThreadItemMedia $value)
 * @method $this setMediaShare(Item $value)
 * @method $this setPlaceholder(Placeholder $value)
 * @method $this setPreviewMedias(Item[] $value)
 * @method $this setProductShare(ProductShare $value)
 * @method $this setProfile(User $value)
 * @method $this setRavenMedia(Item $value)
 * @method $this setReactions(DirectReactions $value)
 * @method $this setReelShare(ReelShare $value)
 * @method $this setSeenUserIds(string[] $value)
 * @method $this setStoryShare(StoryShare $value)
 * @method $this setText(string $value)
 * @method $this setTimestamp(mixed $value)
 * @method $this setUserId(string $value)
 * @method $this setVideoCallEvent(VideoCallEvent $value)
 * @method $this setVoiceMedia(VoiceMedia $value)
 * @method $this unsetActionLog()
 * @method $this unsetAnimatedMedia()
 * @method $this unsetClientContext()
 * @method $this unsetDirectMediaShare()
 * @method $this unsetExpiringMediaActionSummary()
 * @method $this unsetFelixShare()
 * @method $this unsetHideInThread()
 * @method $this unsetItemId()
 * @method $this unsetItemType()
 * @method $this unsetLike()
 * @method $this unsetLink()
 * @method $this unsetLiveVideoShare()
 * @method $this unsetLiveViewerInvite()
 * @method $this unsetLocation()
 * @method $this unsetMedia()
 * @method $this unsetMediaShare()
 * @method $this unsetPlaceholder()
 * @method $this unsetPreviewMedias()
 * @method $this unsetProductShare()
 * @method $this unsetProfile()
 * @method $this unsetRavenMedia()
 * @method $this unsetReactions()
 * @method $this unsetReelShare()
 * @method $this unsetSeenUserIds()
 * @method $this unsetStoryShare()
 * @method $this unsetText()
 * @method $this unsetTimestamp()
 * @method $this unsetUserId()
 * @method $this unsetVideoCallEvent()
 * @method $this unsetVoiceMedia()
 */
class DirectThreadItem extends AutoPropertyMapper
{
    const PLACEHOLDER = 'placeholder';
    const TEXT = 'text';
    const HASHTAG = 'hashtag';
    const LOCATION = 'location';
    const PROFILE = 'profile';
    const MEDIA = 'media';
    const MEDIA_SHARE = 'media_share';
    const EXPIRING_MEDIA = 'raven_media';
    const LIKE = 'like';
    const ACTION_LOG = 'action_log';
    const REACTION = 'reaction';
    const REEL_SHARE = 'reel_share';
    const STORY_SHARE = 'story_share';
    const LINK = 'link';
    const LIVE_VIDEO_SHARE = 'live_video_share';
    const LIVE_VIEWER_INVITE = 'live_viewer_invite';
    const PRODUCT_SHARE = 'product_share';
    const VIDEO_CALL_EVENT = 'video_call_event';
    const VOICE_MEDIA = 'voice_media';

    const JSON_PROPERTY_MAP = [
        'item_id'                       => 'string',
        'item_type'                     => '',
        'text'                          => 'string',
        'media_share'                   => 'Item',
        'preview_medias'                => 'Item[]',
        'media'                         => 'DirectThreadItemMedia',
        'user_id'                       => 'string',
        'timestamp'                     => '',
        'client_context'                => 'string',
        'hide_in_thread'                => '',
        'action_log'                    => 'ActionLog',
        'link'                          => 'DirectLink',
        'reactions'                     => 'DirectReactions',
        'raven_media'                   => 'Item',
        'seen_user_ids'                 => 'string[]',
        'expiring_media_action_summary' => 'DirectExpiringSummary',
        'reel_share'                    => 'ReelShare',
        'placeholder'                   => 'Placeholder',
        'location'                      => 'Location',
        'like'                          => '',
        'live_video_share'              => 'LiveVideoShare',
        'live_viewer_invite'            => 'LiveViewerInvite',
        'profile'                       => 'User',
        'story_share'                   => 'StoryShare',
        'direct_media_share'            => 'MediaShare',
        'video_call_event'              => 'VideoCallEvent',
        'product_share'                 => 'ProductShare',
        'animated_media'                => 'AnimatedMedia',
        'felix_share'                   => 'FelixShare',
        'voice_media'                   => 'VoiceMedia',
    ];
}
