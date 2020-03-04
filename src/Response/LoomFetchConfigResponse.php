<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * LoomFetchConfigResponse.
 *
 * @method int getId()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\SystemControl getSystemControl()
 * @method Model\TraceControl getTraceControl()
 * @method Model\_Message[] get_Messages()
 * @method bool isId()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isSystemControl()
 * @method bool isTraceControl()
 * @method bool is_Messages()
 * @method $this setId(int $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setSystemControl(Model\SystemControl $value)
 * @method $this setTraceControl(Model\TraceControl $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetId()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetSystemControl()
 * @method $this unsetTraceControl()
 * @method $this unset_Messages()
 */
class LoomFetchConfigResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'system_control' => 'Model\SystemControl',
        'trace_control'  => 'Model\TraceControl',
        'id'             => 'int',
    ];
}
