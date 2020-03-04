<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * CreateHighlightResponse.
 *
 * @method mixed getMessage()
 * @method Model\Reel getReel()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isReel()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setReel(Model\Reel $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetReel()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class CreateHighlightResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'reel' => 'Model\Reel',
    ];
}
