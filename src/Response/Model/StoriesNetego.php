<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * StoriesNetego.
 *
 * @method string getHideUnitIfSeen()
 * @method string getId()
 * @method string getTrackingToken()
 * @method bool isHideUnitIfSeen()
 * @method bool isId()
 * @method bool isTrackingToken()
 * @method $this setHideUnitIfSeen(string $value)
 * @method $this setId(string $value)
 * @method $this setTrackingToken(string $value)
 * @method $this unsetHideUnitIfSeen()
 * @method $this unsetId()
 * @method $this unsetTrackingToken()
 */
class StoriesNetego extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'tracking_token'    => 'string',
        'hide_unit_if_seen' => 'string',
        'id'                => 'string',
    ];
}
