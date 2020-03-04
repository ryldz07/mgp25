<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * Used for generic API responses that don't contain any extra data.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class GenericResponse extends Response
{
    // WARNING: Don't add any values here. Create new responses.
    const JSON_PROPERTY_MAP = [];
}
