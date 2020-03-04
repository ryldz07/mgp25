<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * PageInfo.
 *
 * @method string getEndCursor()
 * @method bool getHasNextPage()
 * @method bool getHasPreviousPage()
 * @method bool isEndCursor()
 * @method bool isHasNextPage()
 * @method bool isHasPreviousPage()
 * @method $this setEndCursor(string $value)
 * @method $this setHasNextPage(bool $value)
 * @method $this setHasPreviousPage(bool $value)
 * @method $this unsetEndCursor()
 * @method $this unsetHasNextPage()
 * @method $this unsetHasPreviousPage()
 */
class PageInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'end_cursor'        => 'string',
        'has_next_page'     => 'bool',
        'has_previous_page' => 'bool',
    ];
}
