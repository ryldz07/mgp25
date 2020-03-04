<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SwitchBusinessProfileResponse.
 *
 * @method mixed getMessage()
 * @method mixed getSocialContext()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isSocialContext()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setSocialContext(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetSocialContext()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class SwitchBusinessProfileResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'social_context' => '',
    ];
}
