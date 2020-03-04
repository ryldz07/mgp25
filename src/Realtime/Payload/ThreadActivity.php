<?php

namespace InstagramAPI\Realtime\Payload;

use InstagramAPI\AutoPropertyMapper;

/**
 * ThreadActivity.
 *
 * @method mixed getActivityStatus()
 * @method string getSenderId()
 * @method mixed getTimestamp()
 * @method mixed getTtl()
 * @method bool isActivityStatus()
 * @method bool isSenderId()
 * @method bool isTimestamp()
 * @method bool isTtl()
 * @method $this setActivityStatus(mixed $value)
 * @method $this setSenderId(string $value)
 * @method $this setTimestamp(mixed $value)
 * @method $this setTtl(mixed $value)
 * @method $this unsetActivityStatus()
 * @method $this unsetSenderId()
 * @method $this unsetTimestamp()
 * @method $this unsetTtl()
 */
class ThreadActivity extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'timestamp'       => '',
        'sender_id'       => 'string',
        'activity_status' => '',
        'ttl'             => '',
    ];
}
