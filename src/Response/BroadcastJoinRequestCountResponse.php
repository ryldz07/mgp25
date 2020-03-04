<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * BroadcastJoinRequestCountResponse.
 *
 * @method string getFetchTs()
 * @method mixed getMessage()
 * @method int getNumNewRequests()
 * @method int getNumTotalRequests()
 * @method int getNumUnseenRequests()
 * @method string getStatus()
 * @method Model\User[] getUsers()
 * @method Model\_Message[] get_Messages()
 * @method bool isFetchTs()
 * @method bool isMessage()
 * @method bool isNumNewRequests()
 * @method bool isNumTotalRequests()
 * @method bool isNumUnseenRequests()
 * @method bool isStatus()
 * @method bool isUsers()
 * @method bool is_Messages()
 * @method $this setFetchTs(string $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNumNewRequests(int $value)
 * @method $this setNumTotalRequests(int $value)
 * @method $this setNumUnseenRequests(int $value)
 * @method $this setStatus(string $value)
 * @method $this setUsers(Model\User[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetFetchTs()
 * @method $this unsetMessage()
 * @method $this unsetNumNewRequests()
 * @method $this unsetNumTotalRequests()
 * @method $this unsetNumUnseenRequests()
 * @method $this unsetStatus()
 * @method $this unsetUsers()
 * @method $this unset_Messages()
 */
class BroadcastJoinRequestCountResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'fetch_ts'            => 'string',
        'num_total_requests'  => 'int',
        'num_new_requests'    => 'int',
        'users'               => 'Model\User[]',
        'num_unseen_requests' => 'int',
    ];
}
