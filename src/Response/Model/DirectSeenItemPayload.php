<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectSeenItemPayload.
 *
 * @method mixed getCount()
 * @method string getTimestamp()
 * @method bool isCount()
 * @method bool isTimestamp()
 * @method $this setCount(mixed $value)
 * @method $this setTimestamp(string $value)
 * @method $this unsetCount()
 * @method $this unsetTimestamp()
 */
class DirectSeenItemPayload extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'count'     => '',
        'timestamp' => 'string',
    ];
}
