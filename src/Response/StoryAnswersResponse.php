<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * StoryAnswersResponse.
 *
 * @method mixed getMessage()
 * @method Model\StoryQuestionResponderInfos getResponderInfo()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isResponderInfo()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setResponderInfo(Model\StoryQuestionResponderInfos $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetResponderInfo()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class StoryAnswersResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'responder_info' => 'Model\StoryQuestionResponderInfos',
    ];
}
