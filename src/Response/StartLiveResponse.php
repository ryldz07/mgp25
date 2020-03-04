<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * StartLiveResponse.
 *
 * @method string getMediaId()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMediaId()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMediaId(string $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMediaId()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class StartLiveResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'media_id' => 'string',
    ];
}
