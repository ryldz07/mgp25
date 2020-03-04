<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * LinkContext.
 *
 * @method string getLinkImageUrl()
 * @method string getLinkSummary()
 * @method string getLinkTitle()
 * @method string getLinkUrl()
 * @method bool isLinkImageUrl()
 * @method bool isLinkSummary()
 * @method bool isLinkTitle()
 * @method bool isLinkUrl()
 * @method $this setLinkImageUrl(string $value)
 * @method $this setLinkSummary(string $value)
 * @method $this setLinkTitle(string $value)
 * @method $this setLinkUrl(string $value)
 * @method $this unsetLinkImageUrl()
 * @method $this unsetLinkSummary()
 * @method $this unsetLinkTitle()
 * @method $this unsetLinkUrl()
 */
class LinkContext extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'link_url'       => 'string',
        'link_title'     => 'string',
        'link_summary'   => 'string',
        'link_image_url' => 'string',
    ];
}
