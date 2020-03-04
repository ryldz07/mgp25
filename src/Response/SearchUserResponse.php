<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SearchUserResponse.
 *
 * @method bool getHasMore()
 * @method mixed getMessage()
 * @method int getNumResults()
 * @method string getRankToken()
 * @method string getStatus()
 * @method Model\User[] getUsers()
 * @method Model\_Message[] get_Messages()
 * @method bool isHasMore()
 * @method bool isMessage()
 * @method bool isNumResults()
 * @method bool isRankToken()
 * @method bool isStatus()
 * @method bool isUsers()
 * @method bool is_Messages()
 * @method $this setHasMore(bool $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNumResults(int $value)
 * @method $this setRankToken(string $value)
 * @method $this setStatus(string $value)
 * @method $this setUsers(Model\User[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetHasMore()
 * @method $this unsetMessage()
 * @method $this unsetNumResults()
 * @method $this unsetRankToken()
 * @method $this unsetStatus()
 * @method $this unsetUsers()
 * @method $this unset_Messages()
 */
class SearchUserResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'has_more'    => 'bool',
        'num_results' => 'int',
        'users'       => 'Model\User[]',
        'rank_token'  => 'string',
    ];
}
