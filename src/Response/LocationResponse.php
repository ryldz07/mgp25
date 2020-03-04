<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * LocationResponse.
 *
 * @method mixed getMessage()
 * @method string getRequestId()
 * @method string getStatus()
 * @method Model\Location[] getVenues()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isRequestId()
 * @method bool isStatus()
 * @method bool isVenues()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setRequestId(string $value)
 * @method $this setStatus(string $value)
 * @method $this setVenues(Model\Location[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetRequestId()
 * @method $this unsetStatus()
 * @method $this unsetVenues()
 * @method $this unset_Messages()
 */
class LocationResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'venues'     => 'Model\Location[]',
        'request_id' => 'string',
    ];
}
