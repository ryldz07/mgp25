<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * EndOfFeedDemarcator.
 *
 * @method string getId()
 * @method string getSubtitle()
 * @method string getTitle()
 * @method bool isId()
 * @method bool isSubtitle()
 * @method bool isTitle()
 * @method $this setId(string $value)
 * @method $this setSubtitle(string $value)
 * @method $this setTitle(string $value)
 * @method $this unsetId()
 * @method $this unsetSubtitle()
 * @method $this unsetTitle()
 */
class EndOfFeedDemarcator extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'id'       => 'string',
        'title'    => 'string',
        'subtitle' => 'string',
    ];
}
