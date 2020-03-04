<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * MediaInsights.
 *
 * @method int getAvgEngagementCount()
 * @method int getCommentCount()
 * @method int getEngagementCount()
 * @method int getImpressionCount()
 * @method int getLikeCount()
 * @method int getReachCount()
 * @method int getSaveCount()
 * @method bool isAvgEngagementCount()
 * @method bool isCommentCount()
 * @method bool isEngagementCount()
 * @method bool isImpressionCount()
 * @method bool isLikeCount()
 * @method bool isReachCount()
 * @method bool isSaveCount()
 * @method $this setAvgEngagementCount(int $value)
 * @method $this setCommentCount(int $value)
 * @method $this setEngagementCount(int $value)
 * @method $this setImpressionCount(int $value)
 * @method $this setLikeCount(int $value)
 * @method $this setReachCount(int $value)
 * @method $this setSaveCount(int $value)
 * @method $this unsetAvgEngagementCount()
 * @method $this unsetCommentCount()
 * @method $this unsetEngagementCount()
 * @method $this unsetImpressionCount()
 * @method $this unsetLikeCount()
 * @method $this unsetReachCount()
 * @method $this unsetSaveCount()
 */
class MediaInsights extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'reach_count'          => 'int',
        'impression_count'     => 'int',
        'engagement_count'     => 'int',
        'avg_engagement_count' => 'int',
        'comment_count'        => 'int',
        'save_count'           => 'int',
        'like_count'           => 'int',
    ];
}
