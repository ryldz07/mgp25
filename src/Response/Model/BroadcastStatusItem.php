<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * BroadcastStatusItem.
 *
 * @method string getBroadcastStatus()
 * @method string getCoverFrameUrl()
 * @method bool getHasReducedVisibility()
 * @method string getId()
 * @method int getViewerCount()
 * @method bool isBroadcastStatus()
 * @method bool isCoverFrameUrl()
 * @method bool isHasReducedVisibility()
 * @method bool isId()
 * @method bool isViewerCount()
 * @method $this setBroadcastStatus(string $value)
 * @method $this setCoverFrameUrl(string $value)
 * @method $this setHasReducedVisibility(bool $value)
 * @method $this setId(string $value)
 * @method $this setViewerCount(int $value)
 * @method $this unsetBroadcastStatus()
 * @method $this unsetCoverFrameUrl()
 * @method $this unsetHasReducedVisibility()
 * @method $this unsetId()
 * @method $this unsetViewerCount()
 */
class BroadcastStatusItem extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'broadcast_status'       => 'string',
        'has_reduced_visibility' => 'bool',
        'cover_frame_url'        => 'string',
        'viewer_count'           => 'int',
        'id'                     => 'string',
    ];
}
