<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * ExploreItem.
 *
 * @method Channel getChannel()
 * @method ExploreItemInfo getExploreItemInfo()
 * @method Item getMedia()
 * @method Stories getStories()
 * @method bool isChannel()
 * @method bool isExploreItemInfo()
 * @method bool isMedia()
 * @method bool isStories()
 * @method $this setChannel(Channel $value)
 * @method $this setExploreItemInfo(ExploreItemInfo $value)
 * @method $this setMedia(Item $value)
 * @method $this setStories(Stories $value)
 * @method $this unsetChannel()
 * @method $this unsetExploreItemInfo()
 * @method $this unsetMedia()
 * @method $this unsetStories()
 */
class ExploreItem extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'media'             => 'Item',
        'stories'           => 'Stories',
        'channel'           => 'Channel',
        'explore_item_info' => 'ExploreItemInfo',
    ];
}
