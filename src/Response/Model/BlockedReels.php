<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * BlockedReels.
 *
 * @method mixed getBigList()
 * @method mixed getPageSize()
 * @method User[] getUsers()
 * @method bool isBigList()
 * @method bool isPageSize()
 * @method bool isUsers()
 * @method $this setBigList(mixed $value)
 * @method $this setPageSize(mixed $value)
 * @method $this setUsers(User[] $value)
 * @method $this unsetBigList()
 * @method $this unsetPageSize()
 * @method $this unsetUsers()
 */
class BlockedReels extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'users'     => 'User[]',
        'page_size' => '',
        'big_list'  => '',
    ];
}
