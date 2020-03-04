<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Composer.
 *
 * @method bool getNuxFinished()
 * @method bool isNuxFinished()
 * @method $this setNuxFinished(bool $value)
 * @method $this unsetNuxFinished()
 */
class Composer extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'nux_finished'   => 'bool',
    ];
}
