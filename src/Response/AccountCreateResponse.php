<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * AccountCreateResponse.
 *
 * @method mixed getAccountCreated()
 * @method Model\User getCreatedUser()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isAccountCreated()
 * @method bool isCreatedUser()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setAccountCreated(mixed $value)
 * @method $this setCreatedUser(Model\User $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAccountCreated()
 * @method $this unsetCreatedUser()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class AccountCreateResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'account_created' => '',
        'created_user'    => 'Model\User',
    ];
}
