<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * VerifySMSCodeResponse.
 *
 * @method mixed getMessage()
 * @method string getPhoneNumber()
 * @method string getStatus()
 * @method bool getVerified()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isPhoneNumber()
 * @method bool isStatus()
 * @method bool isVerified()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setPhoneNumber(string $value)
 * @method $this setStatus(string $value)
 * @method $this setVerified(bool $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetPhoneNumber()
 * @method $this unsetStatus()
 * @method $this unsetVerified()
 * @method $this unset_Messages()
 */
class VerifySMSCodeResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'verified'     => 'bool',
        'phone_number' => 'string',
    ];
}
