<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Surface.
 *
 * @method string getName()
 * @method string getRankToken()
 * @method mixed getScores()
 * @method int getTtlSecs()
 * @method bool isName()
 * @method bool isRankToken()
 * @method bool isScores()
 * @method bool isTtlSecs()
 * @method $this setName(string $value)
 * @method $this setRankToken(string $value)
 * @method $this setScores(mixed $value)
 * @method $this setTtlSecs(int $value)
 * @method $this unsetName()
 * @method $this unsetRankToken()
 * @method $this unsetScores()
 * @method $this unsetTtlSecs()
 */
class Surface extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        /*
         * Scores is an array of int/float numbers keyed by user id.
         * It determines how important each user is for sorting purposes.
         */
        'scores'         => '',
        'rank_token'     => 'string',
        'ttl_secs'       => 'int',
        'name'           => 'string',
    ];
}
