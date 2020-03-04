<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * VoterInfo.
 *
 * @method string getMaxId()
 * @method bool getMoreAvailable()
 * @method string getPollId()
 * @method Voter[] getVoters()
 * @method bool isMaxId()
 * @method bool isMoreAvailable()
 * @method bool isPollId()
 * @method bool isVoters()
 * @method $this setMaxId(string $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setPollId(string $value)
 * @method $this setVoters(Voter[] $value)
 * @method $this unsetMaxId()
 * @method $this unsetMoreAvailable()
 * @method $this unsetPollId()
 * @method $this unsetVoters()
 */
class VoterInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'poll_id'           => 'string',
        'voters'            => 'Voter[]',
        'max_id'            => 'string',
        'more_available'    => 'bool',
    ];
}
