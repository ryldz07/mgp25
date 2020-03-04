<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * MegaphoneLogResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method mixed getSuccess()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isSuccess()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setSuccess(mixed $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetSuccess()
 * @method $this unset_Messages()
 */
class MegaphoneLogResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'success' => '',
    ];
}
