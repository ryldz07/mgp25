<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * DirectSendItemResponse.
 *
 * @method mixed getAction()
 * @method mixed getMessage()
 * @method Model\DirectSendItemPayload getPayload()
 * @method string getStatus()
 * @method mixed getStatusCode()
 * @method Model\_Message[] get_Messages()
 * @method bool isAction()
 * @method bool isMessage()
 * @method bool isPayload()
 * @method bool isStatus()
 * @method bool isStatusCode()
 * @method bool is_Messages()
 * @method $this setAction(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setPayload(Model\DirectSendItemPayload $value)
 * @method $this setStatus(string $value)
 * @method $this setStatusCode(mixed $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAction()
 * @method $this unsetMessage()
 * @method $this unsetPayload()
 * @method $this unsetStatus()
 * @method $this unsetStatusCode()
 * @method $this unset_Messages()
 */
class DirectSendItemResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'action'      => '',
        'status_code' => '',
        'payload'     => 'Model\DirectSendItemPayload',
    ];
}
