<?php

namespace InstagramAPI\Realtime\Payload;

use InstagramAPI\AutoPropertyMapper;

/**
 * RealtimeAction.
 *
 * @method string getAction()
 * @method string getStatus()
 * @method bool isAction()
 * @method bool isStatus()
 * @method $this setAction(string $value)
 * @method $this setStatus(string $value)
 * @method $this unsetAction()
 * @method $this unsetStatus()
 */
abstract class RealtimeAction extends AutoPropertyMapper
{
    const ACK = 'item_ack';
    const UNSEEN_COUNT = 'inbox_unseen_count';
    const UNKNOWN = 'unknown';

    const JSON_PROPERTY_MAP = [
        'status' => 'string',
        'action' => 'string',
    ];
}
