<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * CommentResponse.
 *
 * @method Model\Comment getComment()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isComment()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setComment(Model\Comment $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetComment()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class CommentResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'comment' => 'Model\Comment',
    ];
}
