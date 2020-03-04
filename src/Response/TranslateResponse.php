<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TranslateResponse.
 *
 * @method Model\CommentTranslations[] getCommentTranslations()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isCommentTranslations()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setCommentTranslations(Model\CommentTranslations[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetCommentTranslations()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class TranslateResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'comment_translations' => 'Model\CommentTranslations[]',
    ];
}
