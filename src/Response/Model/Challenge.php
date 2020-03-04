<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Challenge.
 *
 * @method mixed getApiPath()
 * @method mixed getHideWebviewHeader()
 * @method mixed getLock()
 * @method mixed getLogout()
 * @method mixed getNativeFlow()
 * @method string getUrl()
 * @method bool isApiPath()
 * @method bool isHideWebviewHeader()
 * @method bool isLock()
 * @method bool isLogout()
 * @method bool isNativeFlow()
 * @method bool isUrl()
 * @method $this setApiPath(mixed $value)
 * @method $this setHideWebviewHeader(mixed $value)
 * @method $this setLock(mixed $value)
 * @method $this setLogout(mixed $value)
 * @method $this setNativeFlow(mixed $value)
 * @method $this setUrl(string $value)
 * @method $this unsetApiPath()
 * @method $this unsetHideWebviewHeader()
 * @method $this unsetLock()
 * @method $this unsetLogout()
 * @method $this unsetNativeFlow()
 * @method $this unsetUrl()
 */
class Challenge extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'url'                 => 'string',
        'api_path'            => '',
        'hide_webview_header' => '',
        'lock'                => '',
        'logout'              => '',
        'native_flow'         => '',
    ];
}
