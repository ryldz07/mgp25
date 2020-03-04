<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * StoryPollVotersResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\VoterInfo getVoterInfo()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isVoterInfo()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setVoterInfo(Model\VoterInfo $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetVoterInfo()
 * @method $this unset_Messages()
 */
class StoryPollVotersResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'voter_info'    => 'Model\VoterInfo',
    ];
}
