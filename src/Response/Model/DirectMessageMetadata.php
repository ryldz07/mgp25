<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectMessageMetadata.
 *
 * @method string getItemId()
 * @method string[] getParticipantIds()
 * @method string getThreadId()
 * @method string getTimestamp()
 * @method bool isItemId()
 * @method bool isParticipantIds()
 * @method bool isThreadId()
 * @method bool isTimestamp()
 * @method $this setItemId(string $value)
 * @method $this setParticipantIds(string[] $value)
 * @method $this setThreadId(string $value)
 * @method $this setTimestamp(string $value)
 * @method $this unsetItemId()
 * @method $this unsetParticipantIds()
 * @method $this unsetThreadId()
 * @method $this unsetTimestamp()
 */
class DirectMessageMetadata extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'thread_id'       => 'string',
        'item_id'         => 'string',
        'timestamp'       => 'string',
        'participant_ids' => 'string[]',
    ];
}
