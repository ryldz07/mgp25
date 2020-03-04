<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * StoryAppAttribution.
 *
 * @method string getAppActionText()
 * @method string getAppIconUrl()
 * @method string getContentUrl()
 * @method string getId()
 * @method string getLink()
 * @method string getName()
 * @method bool isAppActionText()
 * @method bool isAppIconUrl()
 * @method bool isContentUrl()
 * @method bool isId()
 * @method bool isLink()
 * @method bool isName()
 * @method $this setAppActionText(string $value)
 * @method $this setAppIconUrl(string $value)
 * @method $this setContentUrl(string $value)
 * @method $this setId(string $value)
 * @method $this setLink(string $value)
 * @method $this setName(string $value)
 * @method $this unsetAppActionText()
 * @method $this unsetAppIconUrl()
 * @method $this unsetContentUrl()
 * @method $this unsetId()
 * @method $this unsetLink()
 * @method $this unsetName()
 */
class StoryAppAttribution extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'app_action_text'       => 'string',
        'app_icon_url'          => 'string',
        'content_url'           => 'string',
        'id'                    => 'string',
        'link'                  => 'string',
        'name'                  => 'string',
    ];
}
