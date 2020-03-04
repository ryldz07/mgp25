<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * HashtagsResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\Hashtag[] getTags()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isTags()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setTags(Model\Hashtag[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetTags()
 * @method $this unset_Messages()
 */
class HashtagsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'tags' => 'Model\Hashtag[]',
    ];
}
