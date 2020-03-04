<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Thumbnail.
 *
 * @method int getMaxThumbnailsPerSprite()
 * @method int getRenderedWidth()
 * @method int getSpriteHeight()
 * @method string[] getSpriteUrls()
 * @method int getSpriteWidth()
 * @method float getThumbnailDuration()
 * @method int getThumbnailHeight()
 * @method int getThumbnailWidth()
 * @method int getThumbnailsPerRow()
 * @method float getVideoLength()
 * @method bool isMaxThumbnailsPerSprite()
 * @method bool isRenderedWidth()
 * @method bool isSpriteHeight()
 * @method bool isSpriteUrls()
 * @method bool isSpriteWidth()
 * @method bool isThumbnailDuration()
 * @method bool isThumbnailHeight()
 * @method bool isThumbnailWidth()
 * @method bool isThumbnailsPerRow()
 * @method bool isVideoLength()
 * @method $this setMaxThumbnailsPerSprite(int $value)
 * @method $this setRenderedWidth(int $value)
 * @method $this setSpriteHeight(int $value)
 * @method $this setSpriteUrls(string[] $value)
 * @method $this setSpriteWidth(int $value)
 * @method $this setThumbnailDuration(float $value)
 * @method $this setThumbnailHeight(int $value)
 * @method $this setThumbnailWidth(int $value)
 * @method $this setThumbnailsPerRow(int $value)
 * @method $this setVideoLength(float $value)
 * @method $this unsetMaxThumbnailsPerSprite()
 * @method $this unsetRenderedWidth()
 * @method $this unsetSpriteHeight()
 * @method $this unsetSpriteUrls()
 * @method $this unsetSpriteWidth()
 * @method $this unsetThumbnailDuration()
 * @method $this unsetThumbnailHeight()
 * @method $this unsetThumbnailWidth()
 * @method $this unsetThumbnailsPerRow()
 * @method $this unsetVideoLength()
 */
class Thumbnail extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'video_length'              => 'float',
        'thumbnail_width'           => 'int',
        'thumbnail_height'          => 'int',
        'thumbnail_duration'        => 'float',
        'sprite_urls'               => 'string[]',
        'thumbnails_per_row'        => 'int',
        'max_thumbnails_per_sprite' => 'int',
        'sprite_width'              => 'int',
        'sprite_height'             => 'int',
        'rendered_width'            => 'int',
    ];
}
