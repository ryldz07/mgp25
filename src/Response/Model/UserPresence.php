<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * UserPresence.
 *
 * @method string[] getInThreads()
 * @method bool getIsActive()
 * @method string getLastActivityAtMs()
 * @method string getUserId()
 * @method bool isInThreads()
 * @method bool isIsActive()
 * @method bool isLastActivityAtMs()
 * @method bool isUserId()
 * @method $this setInThreads(string[] $value)
 * @method $this setIsActive(bool $value)
 * @method $this setLastActivityAtMs(string $value)
 * @method $this setUserId(string $value)
 * @method $this unsetInThreads()
 * @method $this unsetIsActive()
 * @method $this unsetLastActivityAtMs()
 * @method $this unsetUserId()
 */
class UserPresence extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'user_id'             => 'string',
        'last_activity_at_ms' => 'string',
        'is_active'           => 'bool',
        'in_threads'          => 'string[]',
    ];
}
