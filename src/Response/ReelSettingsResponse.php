<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ReelSettingsResponse.
 *
 * @method Model\BlockedReels getBlockedReels()
 * @method mixed getMessage()
 * @method mixed getMessagePrefs()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isBlockedReels()
 * @method bool isMessage()
 * @method bool isMessagePrefs()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setBlockedReels(Model\BlockedReels $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMessagePrefs(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetBlockedReels()
 * @method $this unsetMessage()
 * @method $this unsetMessagePrefs()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class ReelSettingsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'message_prefs' => '',
        'blocked_reels' => 'Model\BlockedReels',
    ];
}
