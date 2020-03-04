<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * CommentFilterKeywordsResponse.
 *
 * @method mixed getKeywords()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isKeywords()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setKeywords(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetKeywords()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class CommentFilterKeywordsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'keywords' => '',
    ];
}
