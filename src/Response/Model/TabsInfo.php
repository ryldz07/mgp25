<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * TabsInfo.
 *
 * @method string getSelected()
 * @method Tab[] getTabs()
 * @method bool isSelected()
 * @method bool isTabs()
 * @method $this setSelected(string $value)
 * @method $this setTabs(Tab[] $value)
 * @method $this unsetSelected()
 * @method $this unsetTabs()
 */
class TabsInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'tabs'      => 'Tab[]',
        'selected'  => 'string',
    ];
}
