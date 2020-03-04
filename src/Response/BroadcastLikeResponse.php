<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * BroadcastLikeResponse.
 *
 * @method mixed getLikes()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isLikes()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setLikes(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetLikes()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class BroadcastLikeResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'likes' => '',
    ];
}
