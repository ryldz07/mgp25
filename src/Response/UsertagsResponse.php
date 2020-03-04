<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * UsertagsResponse.
 *
 * @method mixed getAutoLoadMoreEnabled()
 * @method Model\Item[] getItems()
 * @method mixed getMessage()
 * @method mixed getMoreAvailable()
 * @method mixed getNewPhotos()
 * @method string getNextMaxId()
 * @method int getNumResults()
 * @method mixed getRequiresReview()
 * @method string getStatus()
 * @method mixed getTotalCount()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNewPhotos()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isRequiresReview()
 * @method bool isStatus()
 * @method bool isTotalCount()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(mixed $value)
 * @method $this setItems(Model\Item[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(mixed $value)
 * @method $this setNewPhotos(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNumResults(int $value)
 * @method $this setRequiresReview(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setTotalCount(mixed $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetItems()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNewPhotos()
 * @method $this unsetNextMaxId()
 * @method $this unsetNumResults()
 * @method $this unsetRequiresReview()
 * @method $this unsetStatus()
 * @method $this unsetTotalCount()
 * @method $this unset_Messages()
 */
class UsertagsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'num_results'            => 'int',
        'auto_load_more_enabled' => '',
        'items'                  => 'Model\Item[]',
        'more_available'         => '',
        'next_max_id'            => 'string',
        'total_count'            => '',
        'requires_review'        => '',
        'new_photos'             => '',
    ];
}
