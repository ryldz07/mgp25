<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * LocationItem.
 *
 * @method Location getLocation()
 * @method mixed getMediaBundles()
 * @method mixed getSubtitle()
 * @method mixed getTitle()
 * @method bool isLocation()
 * @method bool isMediaBundles()
 * @method bool isSubtitle()
 * @method bool isTitle()
 * @method $this setLocation(Location $value)
 * @method $this setMediaBundles(mixed $value)
 * @method $this setSubtitle(mixed $value)
 * @method $this setTitle(mixed $value)
 * @method $this unsetLocation()
 * @method $this unsetMediaBundles()
 * @method $this unsetSubtitle()
 * @method $this unsetTitle()
 */
class LocationItem extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'media_bundles' => '',
        'subtitle'      => '',
        'location'      => 'Location',
        'title'         => '',
    ];
}
