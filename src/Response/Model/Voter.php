<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Voter.
 *
 * @method User getUser()
 * @method int getVote()
 * @method bool isUser()
 * @method bool isVote()
 * @method $this setUser(User $value)
 * @method $this setVote(int $value)
 * @method $this unsetUser()
 * @method $this unsetVote()
 */
class Voter extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'user'  => 'User',
        'vote'  => 'int',
    ];
}
