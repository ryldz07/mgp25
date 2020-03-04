<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FollowingRecentActivityResponse.
 *
 * @method mixed getAutoLoadMoreEnabled()
 * @method mixed getMegaphone()
 * @method mixed getMessage()
 * @method string getNextMaxId()
 * @method string getStatus()
 * @method Model\Story[] getStories()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isMegaphone()
 * @method bool isMessage()
 * @method bool isNextMaxId()
 * @method bool isStatus()
 * @method bool isStories()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(mixed $value)
 * @method $this setMegaphone(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setStatus(string $value)
 * @method $this setStories(Model\Story[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetMegaphone()
 * @method $this unsetMessage()
 * @method $this unsetNextMaxId()
 * @method $this unsetStatus()
 * @method $this unsetStories()
 * @method $this unset_Messages()
 */
class FollowingRecentActivityResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'stories'                => 'Model\Story[]',
        'next_max_id'            => 'string',
        'auto_load_more_enabled' => '',
        'megaphone'              => '',
    ];
}
