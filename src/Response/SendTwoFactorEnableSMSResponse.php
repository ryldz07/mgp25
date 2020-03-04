<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SendTwoFactorEnableSMSResponse.
 *
 * @method mixed getMessage()
 * @method mixed getObfuscatedPhoneNumber()
 * @method Model\PhoneVerificationSettings getPhoneVerificationSettings()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isObfuscatedPhoneNumber()
 * @method bool isPhoneVerificationSettings()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setObfuscatedPhoneNumber(mixed $value)
 * @method $this setPhoneVerificationSettings(Model\PhoneVerificationSettings $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetObfuscatedPhoneNumber()
 * @method $this unsetPhoneVerificationSettings()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class SendTwoFactorEnableSMSResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'phone_verification_settings' => 'Model\PhoneVerificationSettings',
        'obfuscated_phone_number'     => '',
    ];
}
