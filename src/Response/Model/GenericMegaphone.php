<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * GenericMegaphone.
 *
 * @method mixed getActionInfo()
 * @method mixed getBackgroundColor()
 * @method mixed getButtonLayout()
 * @method mixed getButtonLocation()
 * @method Button[] getButtons()
 * @method mixed getDismissible()
 * @method mixed getIcon()
 * @method mixed getMegaphoneVersion()
 * @method mixed getMessage()
 * @method mixed getMessageColor()
 * @method mixed getTitle()
 * @method mixed getTitleColor()
 * @method mixed getType()
 * @method string getUuid()
 * @method bool isActionInfo()
 * @method bool isBackgroundColor()
 * @method bool isButtonLayout()
 * @method bool isButtonLocation()
 * @method bool isButtons()
 * @method bool isDismissible()
 * @method bool isIcon()
 * @method bool isMegaphoneVersion()
 * @method bool isMessage()
 * @method bool isMessageColor()
 * @method bool isTitle()
 * @method bool isTitleColor()
 * @method bool isType()
 * @method bool isUuid()
 * @method $this setActionInfo(mixed $value)
 * @method $this setBackgroundColor(mixed $value)
 * @method $this setButtonLayout(mixed $value)
 * @method $this setButtonLocation(mixed $value)
 * @method $this setButtons(Button[] $value)
 * @method $this setDismissible(mixed $value)
 * @method $this setIcon(mixed $value)
 * @method $this setMegaphoneVersion(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMessageColor(mixed $value)
 * @method $this setTitle(mixed $value)
 * @method $this setTitleColor(mixed $value)
 * @method $this setType(mixed $value)
 * @method $this setUuid(string $value)
 * @method $this unsetActionInfo()
 * @method $this unsetBackgroundColor()
 * @method $this unsetButtonLayout()
 * @method $this unsetButtonLocation()
 * @method $this unsetButtons()
 * @method $this unsetDismissible()
 * @method $this unsetIcon()
 * @method $this unsetMegaphoneVersion()
 * @method $this unsetMessage()
 * @method $this unsetMessageColor()
 * @method $this unsetTitle()
 * @method $this unsetTitleColor()
 * @method $this unsetType()
 * @method $this unsetUuid()
 */
class GenericMegaphone extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'type'              => '',
        'title'             => '',
        'message'           => '',
        'dismissible'       => '',
        'icon'              => '',
        'buttons'           => 'Button[]',
        'megaphone_version' => '',
        'button_layout'     => '',
        'action_info'       => '',
        'button_location'   => '',
        'background_color'  => '',
        'title_color'       => '',
        'message_color'     => '',
        'uuid'              => 'string',
    ];
}
