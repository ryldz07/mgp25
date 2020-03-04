<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * QuestionSticker.
 *
 * @method string getBackgroundColor()
 * @method string getProfilePicUrl()
 * @method string getQuestion()
 * @method string getQuestionId()
 * @method string getQuestionType()
 * @method string getTextColor()
 * @method bool getViewerCanInteract()
 * @method bool isBackgroundColor()
 * @method bool isProfilePicUrl()
 * @method bool isQuestion()
 * @method bool isQuestionId()
 * @method bool isQuestionType()
 * @method bool isTextColor()
 * @method bool isViewerCanInteract()
 * @method $this setBackgroundColor(string $value)
 * @method $this setProfilePicUrl(string $value)
 * @method $this setQuestion(string $value)
 * @method $this setQuestionId(string $value)
 * @method $this setQuestionType(string $value)
 * @method $this setTextColor(string $value)
 * @method $this setViewerCanInteract(bool $value)
 * @method $this unsetBackgroundColor()
 * @method $this unsetProfilePicUrl()
 * @method $this unsetQuestion()
 * @method $this unsetQuestionId()
 * @method $this unsetQuestionType()
 * @method $this unsetTextColor()
 * @method $this unsetViewerCanInteract()
 */
class QuestionSticker extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'question_id'         => 'string',
        'question'            => 'string',
        /*
         * HTML color string such as "#812A2A".
         */
        'text_color'          => 'string',
        /*
         * HTML color string such as "#812A2A".
         */
        'background_color'    => 'string',
        'viewer_can_interact' => 'bool',
        'profile_pic_url'     => 'string',
        'question_type'       => 'string',
    ];
}
