<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * StoryCountdownsResponse.
 *
 * @method Model\CountdownSticker[] getCountdowns()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isCountdowns()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setCountdowns(Model\CountdownSticker[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetCountdowns()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class StoryCountdownsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'countdowns' => 'Model\CountdownSticker[]',
    ];
}
