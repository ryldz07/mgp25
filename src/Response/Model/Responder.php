<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Responder.
 *
 * @method bool getHasSharedResponse()
 * @method string getId()
 * @method string getResponse()
 * @method int getTs()
 * @method User getUser()
 * @method bool isHasSharedResponse()
 * @method bool isId()
 * @method bool isResponse()
 * @method bool isTs()
 * @method bool isUser()
 * @method $this setHasSharedResponse(bool $value)
 * @method $this setId(string $value)
 * @method $this setResponse(string $value)
 * @method $this setTs(int $value)
 * @method $this setUser(User $value)
 * @method $this unsetHasSharedResponse()
 * @method $this unsetId()
 * @method $this unsetResponse()
 * @method $this unsetTs()
 * @method $this unsetUser()
 */
class Responder extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'response'            => 'string',
        'has_shared_response' => 'bool',
        'id'                  => 'string',
        'user'                => 'User',
        'ts'                  => 'int',
    ];
}
