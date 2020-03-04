<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * LikeFeedResponse.
 *
 * @method mixed getAutoLoadMoreEnabled()
 * @method Model\Item[] getItems()
 * @method mixed getLastCountedAt()
 * @method mixed getMessage()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method int getNumResults()
 * @method mixed getPatches()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isLastCountedAt()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isPatches()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(mixed $value)
 * @method $this setItems(Model\Item[] $value)
 * @method $this setLastCountedAt(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNumResults(int $value)
 * @method $this setPatches(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetItems()
 * @method $this unsetLastCountedAt()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetNumResults()
 * @method $this unsetPatches()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class LikeFeedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'auto_load_more_enabled' => '',
        'items'                  => 'Model\Item[]',
        'more_available'         => '',
        'patches'                => '',
        'last_counted_at'        => '',
        'num_results'            => 'int',
        'next_max_id'            => 'string',
    ];
}
