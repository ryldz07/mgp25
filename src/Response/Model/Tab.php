<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Tab.
 *
 * @method string getTitle()
 * @method string getType()
 * @method bool isTitle()
 * @method bool isType()
 * @method $this setTitle(string $value)
 * @method $this setType(string $value)
 * @method $this unsetTitle()
 * @method $this unsetType()
 */
class Tab extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'type'  => 'string',
        'title' => 'string',
    ];
}
