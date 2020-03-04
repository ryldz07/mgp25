<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * IOSLinks.
 *
 * @method string getCanvasDocId()
 * @method int getLinkType()
 * @method bool isCanvasDocId()
 * @method bool isLinkType()
 * @method $this setCanvasDocId(string $value)
 * @method $this setLinkType(int $value)
 * @method $this unsetCanvasDocId()
 * @method $this unsetLinkType()
 */
class IOSLinks extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'linkType'    => 'int',
        'canvasDocId' => 'string',
    ];
}
