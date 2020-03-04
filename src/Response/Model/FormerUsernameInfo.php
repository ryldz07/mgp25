<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * FormerUsernameInfo.
 *
 * @method bool getHasFormerUsernames()
 * @method bool isHasFormerUsernames()
 * @method $this setHasFormerUsernames(bool $value)
 * @method $this unsetHasFormerUsernames()
 */
class FormerUsernameInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'has_former_usernames' => 'bool',
    ];
}
