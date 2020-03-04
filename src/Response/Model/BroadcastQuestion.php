<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * BroadcastQuestion.
 *
 * @method string getQid()
 * @method string getSource()
 * @method string getStoryStickerText()
 * @method string getText()
 * @method string getTimestamp()
 * @method User getUser()
 * @method bool isQid()
 * @method bool isSource()
 * @method bool isStoryStickerText()
 * @method bool isText()
 * @method bool isTimestamp()
 * @method bool isUser()
 * @method $this setQid(string $value)
 * @method $this setSource(string $value)
 * @method $this setStoryStickerText(string $value)
 * @method $this setText(string $value)
 * @method $this setTimestamp(string $value)
 * @method $this setUser(User $value)
 * @method $this unsetQid()
 * @method $this unsetSource()
 * @method $this unsetStoryStickerText()
 * @method $this unsetText()
 * @method $this unsetTimestamp()
 * @method $this unsetUser()
 */
class BroadcastQuestion extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'text'               => 'string',
        'qid'                => 'string',
        'source'             => 'string',
        'user'               => 'User',
        'story_sticker_text' => 'string',
        'timestamp'          => 'string',
    ];
}
