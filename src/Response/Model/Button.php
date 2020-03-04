<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Button.
 *
 * @method mixed getAction()
 * @method mixed getActionInfo()
 * @method mixed getBackgroundColor()
 * @method mixed getBorderColor()
 * @method string getText()
 * @method mixed getTextColor()
 * @method string getUrl()
 * @method bool isAction()
 * @method bool isActionInfo()
 * @method bool isBackgroundColor()
 * @method bool isBorderColor()
 * @method bool isText()
 * @method bool isTextColor()
 * @method bool isUrl()
 * @method $this setAction(mixed $value)
 * @method $this setActionInfo(mixed $value)
 * @method $this setBackgroundColor(mixed $value)
 * @method $this setBorderColor(mixed $value)
 * @method $this setText(string $value)
 * @method $this setTextColor(mixed $value)
 * @method $this setUrl(string $value)
 * @method $this unsetAction()
 * @method $this unsetActionInfo()
 * @method $this unsetBackgroundColor()
 * @method $this unsetBorderColor()
 * @method $this unsetText()
 * @method $this unsetTextColor()
 * @method $this unsetUrl()
 */
class Button extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'text'             => 'string',
        'url'              => 'string',
        'action'           => '',
        'background_color' => '',
        'border_color'     => '',
        'text_color'       => '',
        'action_info'      => '',
    ];
}
