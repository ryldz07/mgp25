<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * PermalinkResponse.
 *
 * @method mixed getMessage()
 * @method string getPermalink()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isPermalink()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setPermalink(string $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetPermalink()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class PermalinkResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'permalink'       => 'string',
    ];
}
