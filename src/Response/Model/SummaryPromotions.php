<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * SummaryPromotions.
 *
 * @method BusinessEdge[] getEdges()
 * @method PageInfo getPageInfo()
 * @method bool isEdges()
 * @method bool isPageInfo()
 * @method $this setEdges(BusinessEdge[] $value)
 * @method $this setPageInfo(PageInfo $value)
 * @method $this unsetEdges()
 * @method $this unsetPageInfo()
 */
class SummaryPromotions extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'edges'     => 'BusinessEdge[]',
        'page_info' => 'PageInfo',
    ];
}
