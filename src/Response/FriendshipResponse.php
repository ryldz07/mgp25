<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FriendshipResponse.
 *
 * @method Model\FriendshipStatus getFriendshipStatus()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isFriendshipStatus()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setFriendshipStatus(Model\FriendshipStatus $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetFriendshipStatus()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class FriendshipResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'friendship_status' => 'Model\FriendshipStatus',
    ];
}
