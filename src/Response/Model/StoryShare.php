<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * StoryShare.
 *
 * @method bool getIsLinked()
 * @method Item getMedia()
 * @method string getMessage()
 * @method string getText()
 * @method string getTitle()
 * @method bool isIsLinked()
 * @method bool isMedia()
 * @method bool isMessage()
 * @method bool isText()
 * @method bool isTitle()
 * @method $this setIsLinked(bool $value)
 * @method $this setMedia(Item $value)
 * @method $this setMessage(string $value)
 * @method $this setText(string $value)
 * @method $this setTitle(string $value)
 * @method $this unsetIsLinked()
 * @method $this unsetMedia()
 * @method $this unsetMessage()
 * @method $this unsetText()
 * @method $this unsetTitle()
 */
class StoryShare extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'media'                            => 'Item',
        'text'                             => 'string',
        'title'                            => 'string',
        'message'                          => 'string',
        'is_linked'                        => 'bool',
    ];
}
