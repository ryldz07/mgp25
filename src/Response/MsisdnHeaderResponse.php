<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * MsisdnHeaderResponse.
 *
 * @method mixed getMessage()
 * @method string getPhoneNumber()
 * @method int getRemainingTtlSeconds()
 * @method string getStatus()
 * @method int getTtl()
 * @method string getUrl()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isPhoneNumber()
 * @method bool isRemainingTtlSeconds()
 * @method bool isStatus()
 * @method bool isTtl()
 * @method bool isUrl()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setPhoneNumber(string $value)
 * @method $this setRemainingTtlSeconds(int $value)
 * @method $this setStatus(string $value)
 * @method $this setTtl(int $value)
 * @method $this setUrl(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetPhoneNumber()
 * @method $this unsetRemainingTtlSeconds()
 * @method $this unsetStatus()
 * @method $this unsetTtl()
 * @method $this unsetUrl()
 * @method $this unset_Messages()
 */
class MsisdnHeaderResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'phone_number'          => 'string',
        'url'                   => 'string',
        'remaining_ttl_seconds' => 'int',
        'ttl'                   => 'int',
    ];
}
