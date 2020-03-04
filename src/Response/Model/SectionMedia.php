<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * SectionMedia.
 *
 * @method Item getMedia()
 * @method bool isMedia()
 * @method $this setMedia(Item $value)
 * @method $this unsetMedia()
 */
class SectionMedia extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'media'  => 'Item',
    ];
}
