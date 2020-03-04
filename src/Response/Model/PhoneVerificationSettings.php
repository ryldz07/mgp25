<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * PhoneVerificationSettings.
 *
 * @method int getMaxSmsCount()
 * @method int getResendSmsDelaySec()
 * @method bool getRobocallAfterMaxSms()
 * @method int getRobocallCountDownTimeSec()
 * @method bool isMaxSmsCount()
 * @method bool isResendSmsDelaySec()
 * @method bool isRobocallAfterMaxSms()
 * @method bool isRobocallCountDownTimeSec()
 * @method $this setMaxSmsCount(int $value)
 * @method $this setResendSmsDelaySec(int $value)
 * @method $this setRobocallAfterMaxSms(bool $value)
 * @method $this setRobocallCountDownTimeSec(int $value)
 * @method $this unsetMaxSmsCount()
 * @method $this unsetResendSmsDelaySec()
 * @method $this unsetRobocallAfterMaxSms()
 * @method $this unsetRobocallCountDownTimeSec()
 */
class PhoneVerificationSettings extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'resend_sms_delay_sec'         => 'int',
        'max_sms_count'                => 'int',
        'robocall_count_down_time_sec' => 'int',
        'robocall_after_max_sms'       => 'bool',
    ];
}
