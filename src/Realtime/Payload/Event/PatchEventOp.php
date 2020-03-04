<?php

namespace InstagramAPI\Realtime\Payload\Event;

use InstagramAPI\AutoPropertyMapper;

/**
 * PatchEventOp.
 *
 * @method mixed getDoublePublish()
 * @method mixed getOp()
 * @method mixed getPath()
 * @method mixed getTs()
 * @method mixed getValue()
 * @method bool isDoublePublish()
 * @method bool isOp()
 * @method bool isPath()
 * @method bool isTs()
 * @method bool isValue()
 * @method $this setDoublePublish(mixed $value)
 * @method $this setOp(mixed $value)
 * @method $this setPath(mixed $value)
 * @method $this setTs(mixed $value)
 * @method $this setValue(mixed $value)
 * @method $this unsetDoublePublish()
 * @method $this unsetOp()
 * @method $this unsetPath()
 * @method $this unsetTs()
 * @method $this unsetValue()
 */
class PatchEventOp extends AutoPropertyMapper
{
    const ADD = 'add';
    const REMOVE = 'remove';
    const REPLACE = 'replace';
    const NOTIFY = 'notify';

    const JSON_PROPERTY_MAP = [
        'op'            => '',
        'path'          => '',
        'value'         => '',
        'ts'            => '',
        'doublePublish' => '',
    ];
}
