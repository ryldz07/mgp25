<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SendSMSCodeResponse.
 *
 * @method mixed getMessage()
 * @method bool getPhoneNumberValid()
 * @method Model\PhoneVerificationSettings getPhoneVerificationSettings()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isPhoneNumberValid()
 * @method bool isPhoneVerificationSettings()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setPhoneNumberValid(bool $value)
 * @method $this setPhoneVerificationSettings(Model\PhoneVerificationSettings $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetPhoneNumberValid()
 * @method $this unsetPhoneVerificationSettings()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class SendSMSCodeResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'phone_number_valid'          => 'bool',
        'phone_verification_settings' => 'Model\PhoneVerificationSettings',
    ];
}
