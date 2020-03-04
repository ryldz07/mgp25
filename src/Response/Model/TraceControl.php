<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * TraceControl.
 *
 * @method int getMaxTraceTimeoutMs()
 * @method bool isMaxTraceTimeoutMs()
 * @method $this setMaxTraceTimeoutMs(int $value)
 * @method $this unsetMaxTraceTimeoutMs()
 */
class TraceControl extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'max_trace_timeout_ms'             => 'int',
    ];
}
