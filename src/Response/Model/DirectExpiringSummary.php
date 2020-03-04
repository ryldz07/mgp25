<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectExpiringSummary.
 *
 * @method int getCount()
 * @method string getTimestamp()
 * @method string getType()
 * @method bool isCount()
 * @method bool isTimestamp()
 * @method bool isType()
 * @method $this setCount(int $value)
 * @method $this setTimestamp(string $value)
 * @method $this setType(string $value)
 * @method $this unsetCount()
 * @method $this unsetTimestamp()
 * @method $this unsetType()
 */
class DirectExpiringSummary extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'type'      => 'string',
        'timestamp' => 'string',
        'count'     => 'int',
    ];
}
