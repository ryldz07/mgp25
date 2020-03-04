<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * BlockedMediaResponse.
 *
 * @method mixed getMediaIds()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMediaIds()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMediaIds(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMediaIds()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class BlockedMediaResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'media_ids' => '',
    ];
}
