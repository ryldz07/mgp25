<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * BootstrapUsersResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\Surface[] getSurfaces()
 * @method Model\User[] getUsers()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isSurfaces()
 * @method bool isUsers()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setSurfaces(Model\Surface[] $value)
 * @method $this setUsers(Model\User[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetSurfaces()
 * @method $this unsetUsers()
 * @method $this unset_Messages()
 */
class BootstrapUsersResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'surfaces' => 'Model\Surface[]',
        'users'    => 'Model\User[]',
    ];
}
