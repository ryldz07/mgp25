<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * RewriteRule.
 *
 * @method string getMatcher()
 * @method string getReplacer()
 * @method bool isMatcher()
 * @method bool isReplacer()
 * @method $this setMatcher(string $value)
 * @method $this setReplacer(string $value)
 * @method $this unsetMatcher()
 * @method $this unsetReplacer()
 */
class RewriteRule extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'matcher'    => 'string',
        'replacer'   => 'string',
    ];
}
