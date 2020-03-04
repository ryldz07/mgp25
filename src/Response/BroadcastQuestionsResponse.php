<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * BroadcastQuestionsResponse.
 *
 * @method mixed getMessage()
 * @method Model\BroadcastQuestion[] getQuestions()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isQuestions()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setQuestions(Model\BroadcastQuestion[] $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetQuestions()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class BroadcastQuestionsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'questions'          => 'Model\BroadcastQuestion[]',
    ];
}
