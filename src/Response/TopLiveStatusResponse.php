<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TopLiveStatusResponse.
 *
 * @method Model\BroadcastStatusItem[] getBroadcastStatusItems()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isBroadcastStatusItems()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setBroadcastStatusItems(Model\BroadcastStatusItem[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetBroadcastStatusItems()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class TopLiveStatusResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'broadcast_status_items' => 'Model\BroadcastStatusItem[]',
    ];
}
