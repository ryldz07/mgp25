<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * ArchivedStoriesFeedItem.
 *
 * @method string getId()
 * @method string getLatestReelMedia()
 * @method int getMediaCount()
 * @method string getReelType()
 * @method string getTimestamp()
 * @method bool isId()
 * @method bool isLatestReelMedia()
 * @method bool isMediaCount()
 * @method bool isReelType()
 * @method bool isTimestamp()
 * @method $this setId(string $value)
 * @method $this setLatestReelMedia(string $value)
 * @method $this setMediaCount(int $value)
 * @method $this setReelType(string $value)
 * @method $this setTimestamp(string $value)
 * @method $this unsetId()
 * @method $this unsetLatestReelMedia()
 * @method $this unsetMediaCount()
 * @method $this unsetReelType()
 * @method $this unsetTimestamp()
 */
class ArchivedStoriesFeedItem extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'timestamp'             => 'string',
        'media_count'           => 'int',
        'id'                    => 'string',
        'reel_type'             => 'string',
        'latest_reel_media'     => 'string',
    ];
}
