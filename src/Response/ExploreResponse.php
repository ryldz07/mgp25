<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ExploreResponse.
 *
 * @method mixed getAutoLoadMoreEnabled()
 * @method Model\ExploreItem[] getItems()
 * @method string getMaxId()
 * @method mixed getMessage()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method int getNumResults()
 * @method string getRankToken()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMaxId()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isRankToken()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(mixed $value)
 * @method $this setItems(Model\ExploreItem[] $value)
 * @method $this setMaxId(string $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNumResults(int $value)
 * @method $this setRankToken(string $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetItems()
 * @method $this unsetMaxId()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetNumResults()
 * @method $this unsetRankToken()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class ExploreResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'num_results'            => 'int',
        'auto_load_more_enabled' => '',
        'items'                  => 'Model\ExploreItem[]',
        'more_available'         => '',
        'next_max_id'            => 'string',
        'max_id'                 => 'string',
        'rank_token'             => 'string',
    ];
}
