<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * CollectionFeedResponse.
 *
 * @method bool getAutoLoadMoreEnabled()
 * @method string getCollectionId()
 * @method string getCollectionName()
 * @method bool getHasRelatedMedia()
 * @method Model\SavedFeedItem[] getItems()
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method string getNextMaxId()
 * @method int getNumResults()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isCollectionId()
 * @method bool isCollectionName()
 * @method bool isHasRelatedMedia()
 * @method bool isItems()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(bool $value)
 * @method $this setCollectionId(string $value)
 * @method $this setCollectionName(string $value)
 * @method $this setHasRelatedMedia(bool $value)
 * @method $this setItems(Model\SavedFeedItem[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNumResults(int $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetCollectionId()
 * @method $this unsetCollectionName()
 * @method $this unsetHasRelatedMedia()
 * @method $this unsetItems()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetNumResults()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class CollectionFeedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'collection_id'          => 'string',
        'collection_name'        => 'string',
        'items'                  => 'Model\SavedFeedItem[]',
        'num_results'            => 'int',
        'more_available'         => 'bool',
        'auto_load_more_enabled' => 'bool',
        'next_max_id'            => 'string',
        'has_related_media'      => 'bool',
    ];
}
