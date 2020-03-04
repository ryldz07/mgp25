<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FBSearchResponse.
 *
 * @method bool getClearClientCache()
 * @method bool getHasMore()
 * @method Model\UserList[] getList()
 * @method mixed getMessage()
 * @method string getRankToken()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isClearClientCache()
 * @method bool isHasMore()
 * @method bool isList()
 * @method bool isMessage()
 * @method bool isRankToken()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setClearClientCache(bool $value)
 * @method $this setHasMore(bool $value)
 * @method $this setList(Model\UserList[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setRankToken(string $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetClearClientCache()
 * @method $this unsetHasMore()
 * @method $this unsetList()
 * @method $this unsetMessage()
 * @method $this unsetRankToken()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class FBSearchResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'has_more'              => 'bool',
        'list'                  => 'Model\UserList[]',
        'clear_client_cache'    => 'bool',
        'has_more'              => 'bool',
        'rank_token'            => 'string',
    ];
}
