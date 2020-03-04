<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * StoryQuestionResponderInfos.
 *
 * @method string getBackgroundColor()
 * @method int getLatestQuestionResponseTime()
 * @method mixed getMaxId()
 * @method bool getMoreAvailable()
 * @method string getQuestion()
 * @method string getQuestionId()
 * @method int getQuestionResponseCount()
 * @method string getQuestionType()
 * @method Responder[] getResponders()
 * @method string getTextColor()
 * @method bool isBackgroundColor()
 * @method bool isLatestQuestionResponseTime()
 * @method bool isMaxId()
 * @method bool isMoreAvailable()
 * @method bool isQuestion()
 * @method bool isQuestionId()
 * @method bool isQuestionResponseCount()
 * @method bool isQuestionType()
 * @method bool isResponders()
 * @method bool isTextColor()
 * @method $this setBackgroundColor(string $value)
 * @method $this setLatestQuestionResponseTime(int $value)
 * @method $this setMaxId(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setQuestion(string $value)
 * @method $this setQuestionId(string $value)
 * @method $this setQuestionResponseCount(int $value)
 * @method $this setQuestionType(string $value)
 * @method $this setResponders(Responder[] $value)
 * @method $this setTextColor(string $value)
 * @method $this unsetBackgroundColor()
 * @method $this unsetLatestQuestionResponseTime()
 * @method $this unsetMaxId()
 * @method $this unsetMoreAvailable()
 * @method $this unsetQuestion()
 * @method $this unsetQuestionId()
 * @method $this unsetQuestionResponseCount()
 * @method $this unsetQuestionType()
 * @method $this unsetResponders()
 * @method $this unsetTextColor()
 */
class StoryQuestionResponderInfos extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'question_id'                   => 'string',
        'question'                      => 'string',
        'question_type'                 => 'string',
        'background_color'              => 'string',
        'text_color'                    => 'string',
        'responders'                    => 'Responder[]',
        'max_id'                        => '',
        'more_available'                => 'bool',
        'question_response_count'       => 'int',
        'latest_question_response_time' => 'int',
    ];
}
