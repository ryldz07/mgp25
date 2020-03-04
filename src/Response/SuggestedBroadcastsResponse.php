<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SuggestedBroadcastsResponse.
 *
 * @method Model\Broadcast[] getBroadcasts()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isBroadcasts()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setBroadcasts(Model\Broadcast[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetBroadcasts()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class SuggestedBroadcastsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'broadcasts' => 'Model\Broadcast[]',
    ];
}
