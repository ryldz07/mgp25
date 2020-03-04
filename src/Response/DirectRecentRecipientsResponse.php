<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * DirectRecentRecipientsResponse.
 *
 * @method mixed getExpirationInterval()
 * @method mixed getMessage()
 * @method mixed getRecentRecipients()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isExpirationInterval()
 * @method bool isMessage()
 * @method bool isRecentRecipients()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setExpirationInterval(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setRecentRecipients(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetExpirationInterval()
 * @method $this unsetMessage()
 * @method $this unsetRecentRecipients()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class DirectRecentRecipientsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'expiration_interval' => '',
        'recent_recipients'   => '',
    ];
}
