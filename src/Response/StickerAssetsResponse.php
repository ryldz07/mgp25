<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * StickerAssetsResponse.
 *
 * @method mixed getMessage()
 * @method Model\StaticStickers[] getStaticStickers()
 * @method string getStatus()
 * @method mixed getVersion()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStaticStickers()
 * @method bool isStatus()
 * @method bool isVersion()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStaticStickers(Model\StaticStickers[] $value)
 * @method $this setStatus(string $value)
 * @method $this setVersion(mixed $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStaticStickers()
 * @method $this unsetStatus()
 * @method $this unsetVersion()
 * @method $this unset_Messages()
 */
class StickerAssetsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'version'         => '',
        'static_stickers' => 'Model\StaticStickers[]',
    ];
}
