<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ArchivedStoriesFeedResponse.
 *
 * @method Model\ArchivedStoriesFeedItem[] getItems()
 * @method string getMaxId()
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method int getNumResults()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isItems()
 * @method bool isMaxId()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNumResults()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setItems(Model\ArchivedStoriesFeedItem[] $value)
 * @method $this setMaxId(string $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNumResults(int $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetItems()
 * @method $this unsetMaxId()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNumResults()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class ArchivedStoriesFeedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'items'                  => 'Model\ArchivedStoriesFeedItem[]',
        'num_results'            => 'int',
        'more_available'         => 'bool',
        'max_id'                 => 'string',
    ];
}
