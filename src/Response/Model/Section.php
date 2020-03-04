<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Section.
 *
 * @method ExploreItemInfo getExploreItemInfo()
 * @method string getFeedType()
 * @method LayoutContent getLayoutContent()
 * @method string getLayoutType()
 * @method bool isExploreItemInfo()
 * @method bool isFeedType()
 * @method bool isLayoutContent()
 * @method bool isLayoutType()
 * @method $this setExploreItemInfo(ExploreItemInfo $value)
 * @method $this setFeedType(string $value)
 * @method $this setLayoutContent(LayoutContent $value)
 * @method $this setLayoutType(string $value)
 * @method $this unsetExploreItemInfo()
 * @method $this unsetFeedType()
 * @method $this unsetLayoutContent()
 * @method $this unsetLayoutType()
 */
class Section extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'layout_type'       => 'string',
        'layout_content'    => 'LayoutContent',
        'feed_type'         => 'string',
        'explore_item_info' => 'ExploreItemInfo',
    ];
}
