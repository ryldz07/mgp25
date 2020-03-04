<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * DirectCreateGroupThreadResponse.
 *
 * @method mixed getCanonical()
 * @method bool getHasNewer()
 * @method bool getHasOlder()
 * @method Model\User getInviter()
 * @method mixed getIsPin()
 * @method Model\DirectThreadItem[] getItems()
 * @method mixed getLastActivityAt()
 * @method mixed getLastSeenAt()
 * @method Model\User[] getLeftUsers()
 * @method mixed getMessage()
 * @method mixed getMuted()
 * @method mixed getNamed()
 * @method mixed getPending()
 * @method string getStatus()
 * @method string getThreadId()
 * @method mixed getThreadTitle()
 * @method mixed getThreadType()
 * @method Model\User[] getUsers()
 * @method string getViewerId()
 * @method Model\_Message[] get_Messages()
 * @method bool isCanonical()
 * @method bool isHasNewer()
 * @method bool isHasOlder()
 * @method bool isInviter()
 * @method bool isIsPin()
 * @method bool isItems()
 * @method bool isLastActivityAt()
 * @method bool isLastSeenAt()
 * @method bool isLeftUsers()
 * @method bool isMessage()
 * @method bool isMuted()
 * @method bool isNamed()
 * @method bool isPending()
 * @method bool isStatus()
 * @method bool isThreadId()
 * @method bool isThreadTitle()
 * @method bool isThreadType()
 * @method bool isUsers()
 * @method bool isViewerId()
 * @method bool is_Messages()
 * @method $this setCanonical(mixed $value)
 * @method $this setHasNewer(bool $value)
 * @method $this setHasOlder(bool $value)
 * @method $this setInviter(Model\User $value)
 * @method $this setIsPin(mixed $value)
 * @method $this setItems(Model\DirectThreadItem[] $value)
 * @method $this setLastActivityAt(mixed $value)
 * @method $this setLastSeenAt(mixed $value)
 * @method $this setLeftUsers(Model\User[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMuted(mixed $value)
 * @method $this setNamed(mixed $value)
 * @method $this setPending(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setThreadId(string $value)
 * @method $this setThreadTitle(mixed $value)
 * @method $this setThreadType(mixed $value)
 * @method $this setUsers(Model\User[] $value)
 * @method $this setViewerId(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetCanonical()
 * @method $this unsetHasNewer()
 * @method $this unsetHasOlder()
 * @method $this unsetInviter()
 * @method $this unsetIsPin()
 * @method $this unsetItems()
 * @method $this unsetLastActivityAt()
 * @method $this unsetLastSeenAt()
 * @method $this unsetLeftUsers()
 * @method $this unsetMessage()
 * @method $this unsetMuted()
 * @method $this unsetNamed()
 * @method $this unsetPending()
 * @method $this unsetStatus()
 * @method $this unsetThreadId()
 * @method $this unsetThreadTitle()
 * @method $this unsetThreadType()
 * @method $this unsetUsers()
 * @method $this unsetViewerId()
 * @method $this unset_Messages()
 */
class DirectCreateGroupThreadResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'thread_id'        => 'string',
        'users'            => 'Model\User[]',
        'left_users'       => 'Model\User[]',
        'items'            => 'Model\DirectThreadItem[]',
        'last_activity_at' => '',
        'muted'            => '',
        'named'            => '',
        'canonical'        => '',
        'pending'          => '',
        'thread_type'      => '',
        'viewer_id'        => 'string',
        'thread_title'     => '',
        'inviter'          => 'Model\User',
        'has_older'        => 'bool',
        'has_newer'        => 'bool',
        'last_seen_at'     => '',
        'is_pin'           => '',
    ];
}
