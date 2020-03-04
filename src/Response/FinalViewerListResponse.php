<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FinalViewerListResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method int getTotalUniqueViewerCount()
 * @method Model\User[] getUsers()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isTotalUniqueViewerCount()
 * @method bool isUsers()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setTotalUniqueViewerCount(int $value)
 * @method $this setUsers(Model\User[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetTotalUniqueViewerCount()
 * @method $this unsetUsers()
 * @method $this unset_Messages()
 */
class FinalViewerListResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'users'                     => 'Model\User[]',
        'total_unique_viewer_count' => 'int',
    ];
}
