<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ReelsMediaResponse.
 *
 * @method mixed getMessage()
 * @method Model\UnpredictableKeys\ReelUnpredictableContainer getReels()
 * @method Model\Reel[] getReelsMedia()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isReels()
 * @method bool isReelsMedia()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setReels(Model\UnpredictableKeys\ReelUnpredictableContainer $value)
 * @method $this setReelsMedia(Model\Reel[] $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetReels()
 * @method $this unsetReelsMedia()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class ReelsMediaResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'reels_media' => 'Model\Reel[]',
        'reels'       => 'Model\UnpredictableKeys\ReelUnpredictableContainer',
    ];
}
