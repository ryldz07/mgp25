<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FaceEffectsResponse.
 *
 * @method Model\Effect[] getEffects()
 * @method Model\Effect getLoadingEffect()
 * @method mixed getMessage()
 * @method mixed getSdkVersion()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isEffects()
 * @method bool isLoadingEffect()
 * @method bool isMessage()
 * @method bool isSdkVersion()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setEffects(Model\Effect[] $value)
 * @method $this setLoadingEffect(Model\Effect $value)
 * @method $this setMessage(mixed $value)
 * @method $this setSdkVersion(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetEffects()
 * @method $this unsetLoadingEffect()
 * @method $this unsetMessage()
 * @method $this unsetSdkVersion()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class FaceEffectsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'sdk_version'    => '',
        'effects'        => 'Model\Effect[]',
        'loading_effect' => 'Model\Effect',
    ];
}
