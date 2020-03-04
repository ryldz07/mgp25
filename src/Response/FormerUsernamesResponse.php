<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FormerUsernamesResponse.
 *
 * @method Model\FormerUsername[] getFormerUsernames()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isFormerUsernames()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setFormerUsernames(Model\FormerUsername[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetFormerUsernames()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class FormerUsernamesResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'former_usernames' => 'Model\FormerUsername[]',
    ];
}
