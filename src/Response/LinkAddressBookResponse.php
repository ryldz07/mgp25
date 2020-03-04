<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * LinkAddressBookResponse.
 *
 * @method Model\Suggestion[] getItems()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isItems()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setItems(Model\Suggestion[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetItems()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class LinkAddressBookResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'items' => 'Model\Suggestion[]',
    ];
}
