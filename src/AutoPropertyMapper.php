<?php

namespace InstagramAPI;

use LazyJsonMapper\LazyJsonMapper;

/**
 * Automatically maps JSON data onto PHP objects with virtual functions.
 *
 * Configures important core settings for the property mapping process.
 */
class AutoPropertyMapper extends LazyJsonMapper
{
    /** @var bool */
    const ALLOW_VIRTUAL_PROPERTIES = false;

    /** @var bool */
    const ALLOW_VIRTUAL_FUNCTIONS = true;

    /** @var bool */
    const USE_MAGIC_LOOKUP_CACHE = true;
}
