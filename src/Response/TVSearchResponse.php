<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TVSearchResponse.
 *
 * @method mixed getMessage()
 * @method int getNumResults()
 * @method string getRankToken()
 * @method Model\TVSearchResult[] getResults()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isNumResults()
 * @method bool isRankToken()
 * @method bool isResults()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setNumResults(int $value)
 * @method $this setRankToken(string $value)
 * @method $this setResults(Model\TVSearchResult[] $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetNumResults()
 * @method $this unsetRankToken()
 * @method $this unsetResults()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class TVSearchResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'results'       => 'Model\TVSearchResult[]',
        'num_results'   => 'int',
        'rank_token'    => 'string',
    ];
}
