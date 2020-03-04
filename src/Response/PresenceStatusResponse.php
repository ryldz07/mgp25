<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * PresenceStatusResponse.
 *
 * @method bool getDisabled()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method bool getThreadPresenceDisabled()
 * @method Model\_Message[] get_Messages()
 * @method bool isDisabled()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isThreadPresenceDisabled()
 * @method bool is_Messages()
 * @method $this setDisabled(bool $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setThreadPresenceDisabled(bool $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetDisabled()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetThreadPresenceDisabled()
 * @method $this unset_Messages()
 */
class PresenceStatusResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'disabled'                 => 'bool',
        'thread_presence_disabled' => 'bool',
    ];
}
