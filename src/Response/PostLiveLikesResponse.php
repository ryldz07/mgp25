<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * PostLiveLikesResponse.
 *
 * @method mixed getEndingOffset()
 * @method mixed getMessage()
 * @method mixed getNextFetchOffset()
 * @method mixed getStartingOffset()
 * @method string getStatus()
 * @method mixed getTimeSeries()
 * @method Model\_Message[] get_Messages()
 * @method bool isEndingOffset()
 * @method bool isMessage()
 * @method bool isNextFetchOffset()
 * @method bool isStartingOffset()
 * @method bool isStatus()
 * @method bool isTimeSeries()
 * @method bool is_Messages()
 * @method $this setEndingOffset(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNextFetchOffset(mixed $value)
 * @method $this setStartingOffset(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setTimeSeries(mixed $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetEndingOffset()
 * @method $this unsetMessage()
 * @method $this unsetNextFetchOffset()
 * @method $this unsetStartingOffset()
 * @method $this unsetStatus()
 * @method $this unsetTimeSeries()
 * @method $this unset_Messages()
 */
class PostLiveLikesResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'starting_offset'   => '',
        'ending_offset'     => '',
        'next_fetch_offset' => '',
        'time_series'       => '',
    ];
}
