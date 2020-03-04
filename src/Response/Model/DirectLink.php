<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectLink.
 *
 * @method LinkContext getLinkContext()
 * @method string getText()
 * @method bool isLinkContext()
 * @method bool isText()
 * @method $this setLinkContext(LinkContext $value)
 * @method $this setText(string $value)
 * @method $this unsetLinkContext()
 * @method $this unsetText()
 */
class DirectLink extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'text'         => 'string',
        'link_context' => 'LinkContext',
    ];
}
