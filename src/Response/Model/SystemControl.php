<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * SystemControl.
 *
 * @method int getUploadBytesPerUpdate()
 * @method int getUploadMaxBytes()
 * @method int getUploadTimePeriodSec()
 * @method bool isUploadBytesPerUpdate()
 * @method bool isUploadMaxBytes()
 * @method bool isUploadTimePeriodSec()
 * @method $this setUploadBytesPerUpdate(int $value)
 * @method $this setUploadMaxBytes(int $value)
 * @method $this setUploadTimePeriodSec(int $value)
 * @method $this unsetUploadBytesPerUpdate()
 * @method $this unsetUploadMaxBytes()
 * @method $this unsetUploadTimePeriodSec()
 */
class SystemControl extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'upload_max_bytes'                     => 'int',
        'upload_time_period_sec'               => 'int',
        'upload_bytes_per_update'              => 'int',
    ];
}
