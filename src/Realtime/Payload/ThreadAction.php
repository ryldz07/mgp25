<?php

namespace InstagramAPI\Realtime\Payload;

use InstagramAPI\AutoPropertyMapper;

/**
 * ThreadAction.
 *
 * @method \InstagramAPI\Response\Model\ActionLog getActionLog()
 * @method string getUserId()
 * @method bool isActionLog()
 * @method bool isUserId()
 * @method $this setActionLog(\InstagramAPI\Response\Model\ActionLog $value)
 * @method $this setUserId(string $value)
 * @method $this unsetActionLog()
 * @method $this unsetUserId()
 */
class ThreadAction extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'user_id'    => 'string',
        'action_log' => '\InstagramAPI\Response\Model\ActionLog',
    ];
}
