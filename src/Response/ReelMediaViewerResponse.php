<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ReelMediaViewerResponse.
 *
 * @method mixed getMessage()
 * @method string getNextMaxId()
 * @method mixed getScreenshotterUserIds()
 * @method string getStatus()
 * @method int getTotalScreenshotCount()
 * @method int getTotalViewerCount()
 * @method Model\Item getUpdatedMedia()
 * @method int getUserCount()
 * @method Model\User[] getUsers()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isNextMaxId()
 * @method bool isScreenshotterUserIds()
 * @method bool isStatus()
 * @method bool isTotalScreenshotCount()
 * @method bool isTotalViewerCount()
 * @method bool isUpdatedMedia()
 * @method bool isUserCount()
 * @method bool isUsers()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setScreenshotterUserIds(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setTotalScreenshotCount(int $value)
 * @method $this setTotalViewerCount(int $value)
 * @method $this setUpdatedMedia(Model\Item $value)
 * @method $this setUserCount(int $value)
 * @method $this setUsers(Model\User[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetNextMaxId()
 * @method $this unsetScreenshotterUserIds()
 * @method $this unsetStatus()
 * @method $this unsetTotalScreenshotCount()
 * @method $this unsetTotalViewerCount()
 * @method $this unsetUpdatedMedia()
 * @method $this unsetUserCount()
 * @method $this unsetUsers()
 * @method $this unset_Messages()
 */
class ReelMediaViewerResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'users'                     => 'Model\User[]',
        'next_max_id'               => 'string',
        'user_count'                => 'int',
        'total_viewer_count'        => 'int',
        'screenshotter_user_ids'    => '',
        'total_screenshot_count'    => 'int',
        'updated_media'             => 'Model\Item',
    ];
}
