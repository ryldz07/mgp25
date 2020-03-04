<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * HideReason.
 *
 * @method string getReason()
 * @method string getText()
 * @method bool isReason()
 * @method bool isText()
 * @method $this setReason(string $value)
 * @method $this setText(string $value)
 * @method $this unsetReason()
 * @method $this unsetText()
 */
class HideReason extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        /*
         * A human string such as "It's not relevant" and "I see it too often".
         */
        'text'   => 'string',
        /*
         * A computer string such as "NOT_RELEVANT" or "KEEP_SEEING_THIS".
         */
        'reason' => 'string',
    ];
}
