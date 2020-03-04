<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * EnableTwoFactorSMSResponse.
 *
 * @method mixed getBackupCodes()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isBackupCodes()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setBackupCodes(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetBackupCodes()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class EnableTwoFactorSMSResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'backup_codes' => '',
    ];
}
