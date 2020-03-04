<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ActiveReelAdsResponse.
 *
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method string getNextMaxId()
 * @method Model\Reel[] getReels()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isReels()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setReels(Model\Reel[] $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetReels()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class ActiveReelAdsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'reels'          => 'Model\Reel[]',
        'next_max_id'    => 'string',
        'more_available' => 'bool',
    ];
}
