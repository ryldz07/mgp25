<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * CommentCategoryFilterResponse.
 *
 * @method mixed getDisabled()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isDisabled()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setDisabled(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetDisabled()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class CommentCategoryFilterResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'disabled' => '',
    ];
}
