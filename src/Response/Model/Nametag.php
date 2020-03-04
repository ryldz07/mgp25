<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Nametag.
 *
 * @method string getEmoji()
 * @method int getGradient()
 * @method int getMode()
 * @method int getSelfieSticker()
 * @method bool isEmoji()
 * @method bool isGradient()
 * @method bool isMode()
 * @method bool isSelfieSticker()
 * @method $this setEmoji(string $value)
 * @method $this setGradient(int $value)
 * @method $this setMode(int $value)
 * @method $this setSelfieSticker(int $value)
 * @method $this unsetEmoji()
 * @method $this unsetGradient()
 * @method $this unsetMode()
 * @method $this unsetSelfieSticker()
 */
class Nametag extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'mode'                 => 'int',
        'gradient'             => 'int',
        'emoji'                => 'string',
        'selfie_sticker'       => 'int',
    ];
}
