<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Reel.
 *
 * @method Broadcast getBroadcast()
 * @method bool getCanReply()
 * @method bool getCanReshare()
 * @method CoverMedia getCoverMedia()
 * @method string getExpiringAt()
 * @method bool getHasBestiesMedia()
 * @method string getId()
 * @method Item[] getItems()
 * @method string getLatestReelMedia()
 * @method Location getLocation()
 * @method int getPrefetchCount()
 * @method string getRankedPosition()
 * @method string getReelType()
 * @method string getSeen()
 * @method string getSeenRankedPosition()
 * @method string getTitle()
 * @method User getUser()
 * @method bool isBroadcast()
 * @method bool isCanReply()
 * @method bool isCanReshare()
 * @method bool isCoverMedia()
 * @method bool isExpiringAt()
 * @method bool isHasBestiesMedia()
 * @method bool isId()
 * @method bool isItems()
 * @method bool isLatestReelMedia()
 * @method bool isLocation()
 * @method bool isPrefetchCount()
 * @method bool isRankedPosition()
 * @method bool isReelType()
 * @method bool isSeen()
 * @method bool isSeenRankedPosition()
 * @method bool isTitle()
 * @method bool isUser()
 * @method $this setBroadcast(Broadcast $value)
 * @method $this setCanReply(bool $value)
 * @method $this setCanReshare(bool $value)
 * @method $this setCoverMedia(CoverMedia $value)
 * @method $this setExpiringAt(string $value)
 * @method $this setHasBestiesMedia(bool $value)
 * @method $this setId(string $value)
 * @method $this setItems(Item[] $value)
 * @method $this setLatestReelMedia(string $value)
 * @method $this setLocation(Location $value)
 * @method $this setPrefetchCount(int $value)
 * @method $this setRankedPosition(string $value)
 * @method $this setReelType(string $value)
 * @method $this setSeen(string $value)
 * @method $this setSeenRankedPosition(string $value)
 * @method $this setTitle(string $value)
 * @method $this setUser(User $value)
 * @method $this unsetBroadcast()
 * @method $this unsetCanReply()
 * @method $this unsetCanReshare()
 * @method $this unsetCoverMedia()
 * @method $this unsetExpiringAt()
 * @method $this unsetHasBestiesMedia()
 * @method $this unsetId()
 * @method $this unsetItems()
 * @method $this unsetLatestReelMedia()
 * @method $this unsetLocation()
 * @method $this unsetPrefetchCount()
 * @method $this unsetRankedPosition()
 * @method $this unsetReelType()
 * @method $this unsetSeen()
 * @method $this unsetSeenRankedPosition()
 * @method $this unsetTitle()
 * @method $this unsetUser()
 */
class Reel extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'id'                => 'string',
        /*
         * Unix "taken_at" timestamp of the newest item in their story reel.
         */
        'latest_reel_media' => 'string',
        /*
         * The "taken_at" timestamp of the last story media you have seen for
         * that user (the current reel's user). Defaults to `0` (not seen).
         */
        'seen'                 => 'string',
        'can_reply'            => 'bool',
        'can_reshare'          => 'bool',
        'reel_type'            => 'string',
        'cover_media'          => 'CoverMedia',
        'user'                 => 'User',
        'items'                => 'Item[]',
        'ranked_position'      => 'string',
        'title'                => 'string',
        'seen_ranked_position' => 'string',
        'expiring_at'          => 'string',
        'has_besties_media'    => 'bool', // Uses int(0) for false and 1 for true.
        'location'             => 'Location',
        'prefetch_count'       => 'int',
        'broadcast'            => 'Broadcast',
    ];
}
