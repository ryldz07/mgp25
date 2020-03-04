<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * DirectSeenItemResponse.
 *
 * @method mixed getAction()
 * @method mixed getMessage()
 * @method Model\DirectSeenItemPayload getPayload()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isAction()
 * @method bool isMessage()
 * @method bool isPayload()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setAction(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setPayload(Model\DirectSeenItemPayload $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAction()
 * @method $this unsetMessage()
 * @method $this unsetPayload()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class DirectSeenItemResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'action'  => '',
        'payload' => 'Model\DirectSeenItemPayload', // The number of unseen items.
    ];
}
