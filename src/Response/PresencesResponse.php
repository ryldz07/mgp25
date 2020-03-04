<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * PresencesResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\UnpredictableKeys\PresenceUnpredictableContainer getUserPresence()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isUserPresence()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setUserPresence(Model\UnpredictableKeys\PresenceUnpredictableContainer $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetUserPresence()
 * @method $this unset_Messages()
 */
class PresencesResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'user_presence' => 'Model\UnpredictableKeys\PresenceUnpredictableContainer',
    ];
}
