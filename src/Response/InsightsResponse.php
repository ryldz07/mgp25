<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * InsightsResponse.
 *
 * @method Model\Insights getInstagramUser()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isInstagramUser()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setInstagramUser(Model\Insights $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetInstagramUser()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class InsightsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'instagram_user' => 'Model\Insights',
    ];
}
