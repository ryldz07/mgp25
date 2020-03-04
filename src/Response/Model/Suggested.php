<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Suggested.
 *
 * @method mixed getClientTime()
 * @method Hashtag getHashtag()
 * @method LocationItem getPlace()
 * @method int getPosition()
 * @method User getUser()
 * @method bool isClientTime()
 * @method bool isHashtag()
 * @method bool isPlace()
 * @method bool isPosition()
 * @method bool isUser()
 * @method $this setClientTime(mixed $value)
 * @method $this setHashtag(Hashtag $value)
 * @method $this setPlace(LocationItem $value)
 * @method $this setPosition(int $value)
 * @method $this setUser(User $value)
 * @method $this unsetClientTime()
 * @method $this unsetHashtag()
 * @method $this unsetPlace()
 * @method $this unsetPosition()
 * @method $this unsetUser()
 */
class Suggested extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'position'     => 'int',
        'hashtag'      => 'Hashtag',
        'user'         => 'User',
        'place'        => 'LocationItem',
        'client_time'  => '',
    ];
}
