<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * MediaCommentsResponse.
 *
 * @method Model\Caption getCaption()
 * @method bool getCaptionIsEdited()
 * @method int getCommentCount()
 * @method bool getCommentLikesEnabled()
 * @method Model\Comment[] getComments()
 * @method bool getHasMoreComments()
 * @method bool getHasMoreHeadloadComments()
 * @method string getMediaHeaderDisplay()
 * @method mixed getMessage()
 * @method string getNextMaxId()
 * @method string getNextMinId()
 * @method mixed getPreviewComments()
 * @method string getStatus()
 * @method bool getThreadingEnabled()
 * @method Model\_Message[] get_Messages()
 * @method bool isCaption()
 * @method bool isCaptionIsEdited()
 * @method bool isCommentCount()
 * @method bool isCommentLikesEnabled()
 * @method bool isComments()
 * @method bool isHasMoreComments()
 * @method bool isHasMoreHeadloadComments()
 * @method bool isMediaHeaderDisplay()
 * @method bool isMessage()
 * @method bool isNextMaxId()
 * @method bool isNextMinId()
 * @method bool isPreviewComments()
 * @method bool isStatus()
 * @method bool isThreadingEnabled()
 * @method bool is_Messages()
 * @method $this setCaption(Model\Caption $value)
 * @method $this setCaptionIsEdited(bool $value)
 * @method $this setCommentCount(int $value)
 * @method $this setCommentLikesEnabled(bool $value)
 * @method $this setComments(Model\Comment[] $value)
 * @method $this setHasMoreComments(bool $value)
 * @method $this setHasMoreHeadloadComments(bool $value)
 * @method $this setMediaHeaderDisplay(string $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNextMinId(string $value)
 * @method $this setPreviewComments(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setThreadingEnabled(bool $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetCaption()
 * @method $this unsetCaptionIsEdited()
 * @method $this unsetCommentCount()
 * @method $this unsetCommentLikesEnabled()
 * @method $this unsetComments()
 * @method $this unsetHasMoreComments()
 * @method $this unsetHasMoreHeadloadComments()
 * @method $this unsetMediaHeaderDisplay()
 * @method $this unsetMessage()
 * @method $this unsetNextMaxId()
 * @method $this unsetNextMinId()
 * @method $this unsetPreviewComments()
 * @method $this unsetStatus()
 * @method $this unsetThreadingEnabled()
 * @method $this unset_Messages()
 */
class MediaCommentsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'comments'                   => 'Model\Comment[]',
        'comment_count'              => 'int',
        'comment_likes_enabled'      => 'bool',
        'next_max_id'                => 'string',
        'next_min_id'                => 'string',
        'caption'                    => 'Model\Caption',
        'has_more_comments'          => 'bool',
        'caption_is_edited'          => 'bool',
        'preview_comments'           => '',
        'has_more_headload_comments' => 'bool',
        'media_header_display'       => 'string',
        'threading_enabled'          => 'bool',
    ];
}
