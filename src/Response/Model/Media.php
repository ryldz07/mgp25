<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Media.
 *
 * @method bool getCommentThreadingEnabled()
 * @method mixed getExpiringAt()
 * @method string getId()
 * @method string getImage()
 * @method User getUser()
 * @method bool isCommentThreadingEnabled()
 * @method bool isExpiringAt()
 * @method bool isId()
 * @method bool isImage()
 * @method bool isUser()
 * @method $this setCommentThreadingEnabled(bool $value)
 * @method $this setExpiringAt(mixed $value)
 * @method $this setId(string $value)
 * @method $this setImage(string $value)
 * @method $this setUser(User $value)
 * @method $this unsetCommentThreadingEnabled()
 * @method $this unsetExpiringAt()
 * @method $this unsetId()
 * @method $this unsetImage()
 * @method $this unsetUser()
 */
class Media extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'image'                            => 'string',
        'id'                               => 'string',
        'user'                             => 'User',
        'expiring_at'                      => '',
        'comment_threading_enabled'        => 'bool',
    ];
}
