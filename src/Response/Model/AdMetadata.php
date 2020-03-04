<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * AdMetadata.
 *
 * @method mixed getType()
 * @method mixed getValue()
 * @method bool isType()
 * @method bool isValue()
 * @method $this setType(mixed $value)
 * @method $this setValue(mixed $value)
 * @method $this unsetType()
 * @method $this unsetValue()
 */
class AdMetadata extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'value' => '',
        'type'  => '',
    ];
}
