<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * _Message.
 *
 * @method mixed getKey()
 * @method mixed getTime()
 * @method bool isKey()
 * @method bool isTime()
 * @method $this setKey(mixed $value)
 * @method $this setTime(mixed $value)
 * @method $this unsetKey()
 * @method $this unsetTime()
 */
class _Message extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'key'  => '',
        'time' => '',
    ];
}
