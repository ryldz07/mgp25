<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * DiscoverTopLiveResponse.
 *
 * @method bool getAutoLoadMoreEnabled()
 * @method Model\Broadcast[] getBroadcasts()
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method string getNextMaxId()
 * @method Model\PostLiveItem[] getPostLiveBroadcasts()
 * @method mixed getScoreMap()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isBroadcasts()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isPostLiveBroadcasts()
 * @method bool isScoreMap()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(bool $value)
 * @method $this setBroadcasts(Model\Broadcast[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setPostLiveBroadcasts(Model\PostLiveItem[] $value)
 * @method $this setScoreMap(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetBroadcasts()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetPostLiveBroadcasts()
 * @method $this unsetScoreMap()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class DiscoverTopLiveResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'broadcasts'             => 'Model\Broadcast[]',
        'post_live_broadcasts'   => 'Model\PostLiveItem[]',
        'score_map'              => '',
        'more_available'         => 'bool',
        'auto_load_more_enabled' => 'bool',
        'next_max_id'            => 'string',
    ];
}
