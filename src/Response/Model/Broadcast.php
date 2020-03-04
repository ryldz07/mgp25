<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Broadcast.
 *
 * @method string getBroadcastMessage()
 * @method User getBroadcastOwner()
 * @method string getBroadcastStatus()
 * @method string getCoverFrameUrl()
 * @method string getDashAbrPlaybackUrl()
 * @method string getDashManifest()
 * @method string getDashPlaybackUrl()
 * @method string getEncodingTag()
 * @method string getExpireAt()
 * @method string getId()
 * @method bool getInternalOnly()
 * @method string getMediaId()
 * @method mixed getMuted()
 * @method int getNumberOfQualities()
 * @method string getOrganicTrackingToken()
 * @method string getPublishedTime()
 * @method mixed getRankedPosition()
 * @method string getRtmpPlaybackUrl()
 * @method mixed getSeenRankedPosition()
 * @method int getTotalUniqueViewerCount()
 * @method int getViewerCount()
 * @method bool isBroadcastMessage()
 * @method bool isBroadcastOwner()
 * @method bool isBroadcastStatus()
 * @method bool isCoverFrameUrl()
 * @method bool isDashAbrPlaybackUrl()
 * @method bool isDashManifest()
 * @method bool isDashPlaybackUrl()
 * @method bool isEncodingTag()
 * @method bool isExpireAt()
 * @method bool isId()
 * @method bool isInternalOnly()
 * @method bool isMediaId()
 * @method bool isMuted()
 * @method bool isNumberOfQualities()
 * @method bool isOrganicTrackingToken()
 * @method bool isPublishedTime()
 * @method bool isRankedPosition()
 * @method bool isRtmpPlaybackUrl()
 * @method bool isSeenRankedPosition()
 * @method bool isTotalUniqueViewerCount()
 * @method bool isViewerCount()
 * @method $this setBroadcastMessage(string $value)
 * @method $this setBroadcastOwner(User $value)
 * @method $this setBroadcastStatus(string $value)
 * @method $this setCoverFrameUrl(string $value)
 * @method $this setDashAbrPlaybackUrl(string $value)
 * @method $this setDashManifest(string $value)
 * @method $this setDashPlaybackUrl(string $value)
 * @method $this setEncodingTag(string $value)
 * @method $this setExpireAt(string $value)
 * @method $this setId(string $value)
 * @method $this setInternalOnly(bool $value)
 * @method $this setMediaId(string $value)
 * @method $this setMuted(mixed $value)
 * @method $this setNumberOfQualities(int $value)
 * @method $this setOrganicTrackingToken(string $value)
 * @method $this setPublishedTime(string $value)
 * @method $this setRankedPosition(mixed $value)
 * @method $this setRtmpPlaybackUrl(string $value)
 * @method $this setSeenRankedPosition(mixed $value)
 * @method $this setTotalUniqueViewerCount(int $value)
 * @method $this setViewerCount(int $value)
 * @method $this unsetBroadcastMessage()
 * @method $this unsetBroadcastOwner()
 * @method $this unsetBroadcastStatus()
 * @method $this unsetCoverFrameUrl()
 * @method $this unsetDashAbrPlaybackUrl()
 * @method $this unsetDashManifest()
 * @method $this unsetDashPlaybackUrl()
 * @method $this unsetEncodingTag()
 * @method $this unsetExpireAt()
 * @method $this unsetId()
 * @method $this unsetInternalOnly()
 * @method $this unsetMediaId()
 * @method $this unsetMuted()
 * @method $this unsetNumberOfQualities()
 * @method $this unsetOrganicTrackingToken()
 * @method $this unsetPublishedTime()
 * @method $this unsetRankedPosition()
 * @method $this unsetRtmpPlaybackUrl()
 * @method $this unsetSeenRankedPosition()
 * @method $this unsetTotalUniqueViewerCount()
 * @method $this unsetViewerCount()
 */
class Broadcast extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'broadcast_owner'           => 'User',
        /*
         * A string such as "active" or "post_live".
         */
        'broadcast_status'          => 'string',
        'cover_frame_url'           => 'string',
        'published_time'            => 'string',
        'broadcast_message'         => 'string',
        'muted'                     => '',
        'media_id'                  => 'string',
        'id'                        => 'string',
        'rtmp_playback_url'         => 'string',
        'dash_abr_playback_url'     => 'string',
        'dash_playback_url'         => 'string',
        'ranked_position'           => '',
        'organic_tracking_token'    => 'string',
        'seen_ranked_position'      => '',
        'viewer_count'              => 'int',
        'dash_manifest'             => 'string',
        /*
         * Unix timestamp of when the "post_live" will expire.
         */
        'expire_at'                 => 'string',
        'encoding_tag'              => 'string',
        'total_unique_viewer_count' => 'int',
        'internal_only'             => 'bool',
        'number_of_qualities'       => 'int',
    ];
}
