<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * LiveComment.
 *
 * @method Comment getComment()
 * @method mixed getEvent()
 * @method mixed getOffset()
 * @method bool isComment()
 * @method bool isEvent()
 * @method bool isOffset()
 * @method $this setComment(Comment $value)
 * @method $this setEvent(mixed $value)
 * @method $this setOffset(mixed $value)
 * @method $this unsetComment()
 * @method $this unsetEvent()
 * @method $this unsetOffset()
 */
class LiveComment extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'comment' => 'Comment',
        'offset'  => '',
        'event'   => '',
    ];
}
