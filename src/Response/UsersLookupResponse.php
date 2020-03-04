<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * UsersLookupResponse.
 *
 * @method bool getCanEmailReset()
 * @method bool getCanSmsReset()
 * @method string getCorrectedInput()
 * @method string getEmail()
 * @method bool getEmailSent()
 * @method bool getHasValidPhone()
 * @method string getLookupSource()
 * @method mixed getMessage()
 * @method string getPhoneNumber()
 * @method string getStatus()
 * @method Model\User getUser()
 * @method string getUserId()
 * @method Model\_Message[] get_Messages()
 * @method bool isCanEmailReset()
 * @method bool isCanSmsReset()
 * @method bool isCorrectedInput()
 * @method bool isEmail()
 * @method bool isEmailSent()
 * @method bool isHasValidPhone()
 * @method bool isLookupSource()
 * @method bool isMessage()
 * @method bool isPhoneNumber()
 * @method bool isStatus()
 * @method bool isUser()
 * @method bool isUserId()
 * @method bool is_Messages()
 * @method $this setCanEmailReset(bool $value)
 * @method $this setCanSmsReset(bool $value)
 * @method $this setCorrectedInput(string $value)
 * @method $this setEmail(string $value)
 * @method $this setEmailSent(bool $value)
 * @method $this setHasValidPhone(bool $value)
 * @method $this setLookupSource(string $value)
 * @method $this setMessage(mixed $value)
 * @method $this setPhoneNumber(string $value)
 * @method $this setStatus(string $value)
 * @method $this setUser(Model\User $value)
 * @method $this setUserId(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetCanEmailReset()
 * @method $this unsetCanSmsReset()
 * @method $this unsetCorrectedInput()
 * @method $this unsetEmail()
 * @method $this unsetEmailSent()
 * @method $this unsetHasValidPhone()
 * @method $this unsetLookupSource()
 * @method $this unsetMessage()
 * @method $this unsetPhoneNumber()
 * @method $this unsetStatus()
 * @method $this unsetUser()
 * @method $this unsetUserId()
 * @method $this unset_Messages()
 */
class UsersLookupResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'user'            => 'Model\User',
        'email_sent'      => 'bool',
        'has_valid_phone' => 'bool',
        'can_email_reset' => 'bool',
        'can_sms_reset'   => 'bool',
        'user_id'         => 'string',
        'lookup_source'   => 'string',
        'email'           => 'string',
        'phone_number'    => 'string',
        'corrected_input' => 'string',
    ];
}
