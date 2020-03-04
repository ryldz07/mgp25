<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectReactions.
 *
 * @method DirectReaction[] getLikes()
 * @method int getLikesCount()
 * @method bool isLikes()
 * @method bool isLikesCount()
 * @method $this setLikes(DirectReaction[] $value)
 * @method $this setLikesCount(int $value)
 * @method $this unsetLikes()
 * @method $this unsetLikesCount()
 */
class DirectReactions extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'likes_count' => 'int',
        'likes'       => 'DirectReaction[]',
    ];
}
