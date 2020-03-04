<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * TopLive.
 *
 * @method User[] getBroadcastOwners()
 * @method mixed getRankedPosition()
 * @method bool isBroadcastOwners()
 * @method bool isRankedPosition()
 * @method $this setBroadcastOwners(User[] $value)
 * @method $this setRankedPosition(mixed $value)
 * @method $this unsetBroadcastOwners()
 * @method $this unsetRankedPosition()
 */
class TopLive extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'broadcast_owners' => 'User[]',
        'ranked_position'  => '',
    ];
}
