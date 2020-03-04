<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * CommentTranslations.
 *
 * @method string getId()
 * @method mixed getTranslation()
 * @method bool isId()
 * @method bool isTranslation()
 * @method $this setId(string $value)
 * @method $this setTranslation(mixed $value)
 * @method $this unsetId()
 * @method $this unsetTranslation()
 */
class CommentTranslations extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'id'          => 'string',
        'translation' => '',
    ];
}
