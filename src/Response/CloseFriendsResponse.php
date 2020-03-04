<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * CloseFriendsResponse.
 *
 * @method mixed getBigList()
 * @method mixed getMessage()
 * @method string getNextMaxId()
 * @method mixed getPageSize()
 * @method mixed getSections()
 * @method string getStatus()
 * @method Model\User[] getUsers()
 * @method Model\_Message[] get_Messages()
 * @method bool isBigList()
 * @method bool isMessage()
 * @method bool isNextMaxId()
 * @method bool isPageSize()
 * @method bool isSections()
 * @method bool isStatus()
 * @method bool isUsers()
 * @method bool is_Messages()
 * @method $this setBigList(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setPageSize(mixed $value)
 * @method $this setSections(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setUsers(Model\User[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetBigList()
 * @method $this unsetMessage()
 * @method $this unsetNextMaxId()
 * @method $this unsetPageSize()
 * @method $this unsetSections()
 * @method $this unsetStatus()
 * @method $this unsetUsers()
 * @method $this unset_Messages()
 */
class CloseFriendsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        Model\CloseFriends::class, // Import property map.
        'next_max_id' => 'string',
    ];
}
