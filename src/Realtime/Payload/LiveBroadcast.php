<?php

namespace InstagramAPI\Realtime\Payload;

use InstagramAPI\AutoPropertyMapper;

/**
 * LiveBroadcast.
 *
 * @method string getBroadcastId()
 * @method string getBroadcastMessage()
 * @method mixed getDisplayNotification()
 * @method mixed getIsPeriodic()
 * @method \InstagramAPI\Response\Model\User getUser()
 * @method bool isBroadcastId()
 * @method bool isBroadcastMessage()
 * @method bool isDisplayNotification()
 * @method bool isIsPeriodic()
 * @method bool isUser()
 * @method $this setBroadcastId(string $value)
 * @method $this setBroadcastMessage(string $value)
 * @method $this setDisplayNotification(mixed $value)
 * @method $this setIsPeriodic(mixed $value)
 * @method $this setUser(\InstagramAPI\Response\Model\User $value)
 * @method $this unsetBroadcastId()
 * @method $this unsetBroadcastMessage()
 * @method $this unsetDisplayNotification()
 * @method $this unsetIsPeriodic()
 * @method $this unsetUser()
 */
class LiveBroadcast extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'user'                 => '\InstagramAPI\Response\Model\User',
        'broadcast_id'         => 'string',
        'is_periodic'          => '',
        'broadcast_message'    => 'string',
        'display_notification' => '',
    ];
}
