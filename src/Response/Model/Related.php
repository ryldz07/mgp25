<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Related.
 *
 * @method string getId()
 * @method mixed getName()
 * @method mixed getType()
 * @method bool isId()
 * @method bool isName()
 * @method bool isType()
 * @method $this setId(string $value)
 * @method $this setName(mixed $value)
 * @method $this setType(mixed $value)
 * @method $this unsetId()
 * @method $this unsetName()
 * @method $this unsetType()
 */
class Related extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'name' => '',
        'id'   => 'string',
        'type' => '',
    ];
}
