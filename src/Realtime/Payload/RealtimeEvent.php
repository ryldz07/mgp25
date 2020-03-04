<?php

namespace InstagramAPI\Realtime\Payload;

use InstagramAPI\AutoPropertyMapper;

/**
 * RealtimeEvent.
 *
 * @method string getEvent()
 * @method bool isEvent()
 * @method $this setEvent(string $value)
 * @method $this unsetEvent()
 */
abstract class RealtimeEvent extends AutoPropertyMapper
{
    const SUBSCRIBED = 'subscribed';
    const UNSUBSCRIBED = 'unsubscribed';
    const KEEPALIVE = 'keepalive';
    const PATCH = 'patch';
    const BROADCAST_ACK = 'broadcast-ack';
    const ERROR = 'error';

    const JSON_PROPERTY_MAP = [
        'event' => 'string',
    ];
}
