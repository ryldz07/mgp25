<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * TraySuggestions.
 *
 * @method string getBannerSubtitle()
 * @method string getBannerTitle()
 * @method string getSuggestionType()
 * @method StoryTray[] getTray()
 * @method string getTrayTitle()
 * @method bool isBannerSubtitle()
 * @method bool isBannerTitle()
 * @method bool isSuggestionType()
 * @method bool isTray()
 * @method bool isTrayTitle()
 * @method $this setBannerSubtitle(string $value)
 * @method $this setBannerTitle(string $value)
 * @method $this setSuggestionType(string $value)
 * @method $this setTray(StoryTray[] $value)
 * @method $this setTrayTitle(string $value)
 * @method $this unsetBannerSubtitle()
 * @method $this unsetBannerTitle()
 * @method $this unsetSuggestionType()
 * @method $this unsetTray()
 * @method $this unsetTrayTitle()
 */
class TraySuggestions extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'tray'            => 'StoryTray[]',
        'tray_title'      => 'string',
        'banner_title'    => 'string',
        'banner_subtitle' => 'string',
        'suggestion_type' => 'string',
    ];
}
