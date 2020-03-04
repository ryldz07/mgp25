<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Placeholder.
 *
 * @method bool getIsLinked()
 * @method string getMessage()
 * @method string getTitle()
 * @method bool isIsLinked()
 * @method bool isMessage()
 * @method bool isTitle()
 * @method $this setIsLinked(bool $value)
 * @method $this setMessage(string $value)
 * @method $this setTitle(string $value)
 * @method $this unsetIsLinked()
 * @method $this unsetMessage()
 * @method $this unsetTitle()
 */
class Placeholder extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'is_linked' => 'bool',
        'title'     => 'string',
        'message'   => 'string',
    ];
}
