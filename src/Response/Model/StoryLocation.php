<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;
use InstagramAPI\Response\PropertyCollection;

/**
 * StoryLocation.
 *
 * @method string getAttribution()
 * @method float getHeight()
 * @method int getIsHidden()
 * @method int getIsPinned()
 * @method Location getLocation()
 * @method float getRotation()
 * @method float getWidth()
 * @method float getX()
 * @method float getY()
 * @method float getZ()
 * @method bool isAttribution()
 * @method bool isHeight()
 * @method bool isIsHidden()
 * @method bool isIsPinned()
 * @method bool isLocation()
 * @method bool isRotation()
 * @method bool isWidth()
 * @method bool isX()
 * @method bool isY()
 * @method bool isZ()
 * @method $this setAttribution(string $value)
 * @method $this setHeight(float $value)
 * @method $this setIsHidden(int $value)
 * @method $this setIsPinned(int $value)
 * @method $this setLocation(Location $value)
 * @method $this setRotation(float $value)
 * @method $this setWidth(float $value)
 * @method $this setX(float $value)
 * @method $this setY(float $value)
 * @method $this setZ(float $value)
 * @method $this unsetAttribution()
 * @method $this unsetHeight()
 * @method $this unsetIsHidden()
 * @method $this unsetIsPinned()
 * @method $this unsetLocation()
 * @method $this unsetRotation()
 * @method $this unsetWidth()
 * @method $this unsetX()
 * @method $this unsetY()
 * @method $this unsetZ()
 */
class StoryLocation extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        PropertyCollection\Sticker::class,
        'location'    => 'Location',
        'attribution' => 'string',
        'is_hidden'   => 'int',
    ];
}
