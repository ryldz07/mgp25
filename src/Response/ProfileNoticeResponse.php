<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ProfileNoticeResponse.
 *
 * @method bool getHasChangePasswordMegaphone()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isHasChangePasswordMegaphone()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setHasChangePasswordMegaphone(bool $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetHasChangePasswordMegaphone()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class ProfileNoticeResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'has_change_password_megaphone' => 'bool',
    ];
}
