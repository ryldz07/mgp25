<?php

namespace InstagramAPI\Realtime\Payload;

use InstagramAPI\AutoPropertyMapper;

/**
 * StoryScreenshot.
 *
 * @method \InstagramAPI\Response\Model\User getActionUserDict()
 * @method int getMediaType()
 * @method bool isActionUserDict()
 * @method bool isMediaType()
 * @method $this setActionUserDict(\InstagramAPI\Response\Model\User $value)
 * @method $this setMediaType(int $value)
 * @method $this unsetActionUserDict()
 * @method $this unsetMediaType()
 */
class StoryScreenshot extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'action_user_dict' => '\InstagramAPI\Response\Model\User',
        /*
         * A number describing what type of media this is.
         */
        'media_type'       => 'int',
    ];
}
