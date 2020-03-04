<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TokenResultResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\Token getToken()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isToken()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setToken(Model\Token $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetToken()
 * @method $this unset_Messages()
 */
class TokenResultResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'token' => 'Model\Token',
    ];
}
