<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SearchTagResponse.
 *
 * @method bool getHasMore()
 * @method mixed getMessage()
 * @method string getRankToken()
 * @method Model\Tag[] getResults()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isHasMore()
 * @method bool isMessage()
 * @method bool isRankToken()
 * @method bool isResults()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setHasMore(bool $value)
 * @method $this setMessage(mixed $value)
 * @method $this setRankToken(string $value)
 * @method $this setResults(Model\Tag[] $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetHasMore()
 * @method $this unsetMessage()
 * @method $this unsetRankToken()
 * @method $this unsetResults()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class SearchTagResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'has_more'   => 'bool',
        'results'    => 'Model\Tag[]',
        'rank_token' => 'string',
    ];
}
