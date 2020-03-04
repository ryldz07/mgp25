<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectInbox.
 *
 * @method bool getBlendedInboxEnabled()
 * @method bool getHasOlder()
 * @method mixed getOldestCursor()
 * @method DirectThread[] getThreads()
 * @method mixed getUnseenCount()
 * @method mixed getUnseenCountTs()
 * @method bool isBlendedInboxEnabled()
 * @method bool isHasOlder()
 * @method bool isOldestCursor()
 * @method bool isThreads()
 * @method bool isUnseenCount()
 * @method bool isUnseenCountTs()
 * @method $this setBlendedInboxEnabled(bool $value)
 * @method $this setHasOlder(bool $value)
 * @method $this setOldestCursor(mixed $value)
 * @method $this setThreads(DirectThread[] $value)
 * @method $this setUnseenCount(mixed $value)
 * @method $this setUnseenCountTs(mixed $value)
 * @method $this unsetBlendedInboxEnabled()
 * @method $this unsetHasOlder()
 * @method $this unsetOldestCursor()
 * @method $this unsetThreads()
 * @method $this unsetUnseenCount()
 * @method $this unsetUnseenCountTs()
 */
class DirectInbox extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'has_older'             => 'bool',
        'unseen_count'          => '',
        'unseen_count_ts'       => '', // Is a timestamp.
        'blended_inbox_enabled' => 'bool',
        'oldest_cursor'         => '',
        'threads'               => 'DirectThread[]',
    ];
}
