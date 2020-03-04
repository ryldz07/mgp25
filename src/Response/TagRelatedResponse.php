<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TagRelatedResponse.
 *
 * @method mixed getMessage()
 * @method Model\Related[] getRelated()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isRelated()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setRelated(Model\Related[] $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetRelated()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class TagRelatedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'related' => 'Model\Related[]',
    ];
}
