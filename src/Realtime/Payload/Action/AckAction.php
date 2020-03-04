<?php

namespace InstagramAPI\Realtime\Payload\Action;

use InstagramAPI\Realtime\Payload\RealtimeAction;

/**
 * AckAction.
 *
 * @method string getAction()
 * @method \InstagramAPI\Response\Model\DirectSendItemPayload getPayload()
 * @method string getStatus()
 * @method mixed getStatusCode()
 * @method bool isAction()
 * @method bool isPayload()
 * @method bool isStatus()
 * @method bool isStatusCode()
 * @method $this setAction(string $value)
 * @method $this setPayload(\InstagramAPI\Response\Model\DirectSendItemPayload $value)
 * @method $this setStatus(string $value)
 * @method $this setStatusCode(mixed $value)
 * @method $this unsetAction()
 * @method $this unsetPayload()
 * @method $this unsetStatus()
 * @method $this unsetStatusCode()
 */
class AckAction extends RealtimeAction
{
    const JSON_PROPERTY_MAP = [
        'status_code' => '',
        'payload'     => '\InstagramAPI\Response\Model\DirectSendItemPayload',
    ];
}
