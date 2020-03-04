<?php

namespace InstagramAPI\Realtime\Payload\Event;

use InstagramAPI\Realtime\Payload\RealtimeEvent;

/**
 * PatchEvent.
 *
 * @method PatchEventOp[] getData()
 * @method string getEvent()
 * @method bool getLazy()
 * @method int getMessageType()
 * @method int getNumEndpoints()
 * @method int getSeqId()
 * @method bool isData()
 * @method bool isEvent()
 * @method bool isLazy()
 * @method bool isMessageType()
 * @method bool isNumEndpoints()
 * @method bool isSeqId()
 * @method $this setData(PatchEventOp[] $value)
 * @method $this setEvent(string $value)
 * @method $this setLazy(bool $value)
 * @method $this setMessageType(int $value)
 * @method $this setNumEndpoints(int $value)
 * @method $this setSeqId(int $value)
 * @method $this unsetData()
 * @method $this unsetEvent()
 * @method $this unsetLazy()
 * @method $this unsetMessageType()
 * @method $this unsetNumEndpoints()
 * @method $this unsetSeqId()
 */
class PatchEvent extends RealtimeEvent
{
    const JSON_PROPERTY_MAP = [
        'data'          => 'PatchEventOp[]',
        'message_type'  => 'int',
        'seq_id'        => 'int',
        'lazy'          => 'bool',
        'num_endpoints' => 'int',
    ];
}
