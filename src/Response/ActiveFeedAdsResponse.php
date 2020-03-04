<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ActiveFeedAdsResponse.
 *
 * @method Model\FeedItem[] getFeedItems()
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method string getNextMaxId()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isFeedItems()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setFeedItems(Model\FeedItem[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetFeedItems()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class ActiveFeedAdsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'feed_items'     => 'Model\FeedItem[]',
        'next_max_id'    => 'string',
        'more_available' => 'bool',
    ];
}
