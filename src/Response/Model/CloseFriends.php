<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * CloseFriends.
 *
 * @method mixed getBigList()
 * @method mixed getPageSize()
 * @method mixed getSections()
 * @method User[] getUsers()
 * @method bool isBigList()
 * @method bool isPageSize()
 * @method bool isSections()
 * @method bool isUsers()
 * @method $this setBigList(mixed $value)
 * @method $this setPageSize(mixed $value)
 * @method $this setSections(mixed $value)
 * @method $this setUsers(User[] $value)
 * @method $this unsetBigList()
 * @method $this unsetPageSize()
 * @method $this unsetSections()
 * @method $this unsetUsers()
 */
class CloseFriends extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'sections'  => '',
        'users'     => 'User[]',
        'big_list'  => '',
        'page_size' => '',
    ];
}
