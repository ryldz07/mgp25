<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * EnableDisableLiveCommentsResponse.
 *
 * @method int getCommentMuted()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isCommentMuted()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setCommentMuted(int $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetCommentMuted()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class EnableDisableLiveCommentsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'comment_muted' => 'int',
    ];
}
