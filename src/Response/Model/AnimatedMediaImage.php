<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * AnimatedMediaImage.
 *
 * @method AnimatedMediaImageFixedHeigth getFixedHeight()
 * @method bool isFixedHeight()
 * @method $this setFixedHeight(AnimatedMediaImageFixedHeigth $value)
 * @method $this unsetFixedHeight()
 */
class AnimatedMediaImage extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'fixed_height'  => 'AnimatedMediaImageFixedHeigth',
    ];
}
