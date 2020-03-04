<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Stories.
 *
 * @method string getId()
 * @method mixed getIsPortrait()
 * @method TopLive getTopLive()
 * @method StoryTray[] getTray()
 * @method bool isId()
 * @method bool isIsPortrait()
 * @method bool isTopLive()
 * @method bool isTray()
 * @method $this setId(string $value)
 * @method $this setIsPortrait(mixed $value)
 * @method $this setTopLive(TopLive $value)
 * @method $this setTray(StoryTray[] $value)
 * @method $this unsetId()
 * @method $this unsetIsPortrait()
 * @method $this unsetTopLive()
 * @method $this unsetTray()
 */
class Stories extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'is_portrait' => '',
        'tray'        => 'StoryTray[]',
        'id'          => 'string',
        'top_live'    => 'TopLive',
    ];
}
