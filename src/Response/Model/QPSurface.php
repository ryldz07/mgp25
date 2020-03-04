<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * QPSurface.
 *
 * @method int getCooldown()
 * @method int getSurfaceId()
 * @method bool isCooldown()
 * @method bool isSurfaceId()
 * @method $this setCooldown(int $value)
 * @method $this setSurfaceId(int $value)
 * @method $this unsetCooldown()
 * @method $this unsetSurfaceId()
 */
class QPSurface extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'surface_id'             => 'int',
        'cooldown'               => 'int',
    ];
}
