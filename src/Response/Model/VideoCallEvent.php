<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * VideoCallEvent.
 *
 * @method string getAction()
 * @method string getVcId()
 * @method bool isAction()
 * @method bool isVcId()
 * @method $this setAction(string $value)
 * @method $this setVcId(string $value)
 * @method $this unsetAction()
 * @method $this unsetVcId()
 */
class VideoCallEvent extends AutoPropertyMapper
{
    const VIDEO_CALL_STARTED = 'video_call_started';
    const VIDEO_CALL_JOINED = 'video_call_joined';
    const VIDEO_CALL_LEFT = 'video_call_left';
    const VIDEO_CALL_ENDED = 'video_call_ended';
    const UNKNOWN = 'unknown';

    const JSON_PROPERTY_MAP = [
        'action' => 'string',
        'vc_id'  => 'string',
    ];
}
