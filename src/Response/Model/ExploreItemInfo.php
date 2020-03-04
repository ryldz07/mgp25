<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * ExploreItemInfo.
 *
 * @method int getAspectRatio()
 * @method bool getAutoplay()
 * @method string getDestinationView()
 * @method int getNumColumns()
 * @method int getTotalNumColumns()
 * @method bool isAspectRatio()
 * @method bool isAutoplay()
 * @method bool isDestinationView()
 * @method bool isNumColumns()
 * @method bool isTotalNumColumns()
 * @method $this setAspectRatio(int $value)
 * @method $this setAutoplay(bool $value)
 * @method $this setDestinationView(string $value)
 * @method $this setNumColumns(int $value)
 * @method $this setTotalNumColumns(int $value)
 * @method $this unsetAspectRatio()
 * @method $this unsetAutoplay()
 * @method $this unsetDestinationView()
 * @method $this unsetNumColumns()
 * @method $this unsetTotalNumColumns()
 */
class ExploreItemInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'num_columns'       => 'int',
        'total_num_columns' => 'int',
        'aspect_ratio'      => 'int',
        'autoplay'          => 'bool',
        'destination_view'  => 'string',
    ];
}
