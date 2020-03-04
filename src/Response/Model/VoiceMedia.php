<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * VoiceMedia.
 *
 * @method DirectThreadItemMedia getMedia()
 * @method bool isMedia()
 * @method $this setMedia(DirectThreadItemMedia $value)
 * @method $this unsetMedia()
 */
class VoiceMedia extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'media'                            => 'DirectThreadItemMedia',
    ];
}
