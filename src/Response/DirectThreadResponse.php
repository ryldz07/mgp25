<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * DirectThreadResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\DirectThread getThread()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isThread()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setThread(Model\DirectThread $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetThread()
 * @method $this unset_Messages()
 */
class DirectThreadResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'thread' => 'Model\DirectThread',
    ];
}
