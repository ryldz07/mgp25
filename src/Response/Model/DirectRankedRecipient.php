<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectRankedRecipient.
 *
 * @method DirectThread getThread()
 * @method User getUser()
 * @method bool isThread()
 * @method bool isUser()
 * @method $this setThread(DirectThread $value)
 * @method $this setUser(User $value)
 * @method $this unsetThread()
 * @method $this unsetUser()
 */
class DirectRankedRecipient extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'thread' => 'DirectThread',
        'user'   => 'User',
    ];
}
