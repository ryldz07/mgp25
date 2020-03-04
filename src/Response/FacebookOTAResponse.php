<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FacebookOTAResponse.
 *
 * @method mixed getBundles()
 * @method mixed getMessage()
 * @method string getRequestId()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isBundles()
 * @method bool isMessage()
 * @method bool isRequestId()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setBundles(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setRequestId(string $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetBundles()
 * @method $this unsetMessage()
 * @method $this unsetRequestId()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class FacebookOTAResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'bundles'    => '',
        'request_id' => 'string',
    ];
}
