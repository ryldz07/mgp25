<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Comment.
 *
 * @method int getBitFlags()
 * @method int getChildCommentCount()
 * @method int getCommentLikeCount()
 * @method string getContentType()
 * @method string getCreatedAt()
 * @method string getCreatedAtUtc()
 * @method bool getDidReportAsSpam()
 * @method bool getHasLikedComment()
 * @method bool getHasMoreHeadChildComments()
 * @method bool getHasMoreTailChildComments()
 * @method bool getHasTranslation()
 * @method string getInlineComposerDisplayCondition()
 * @method string getMediaId()
 * @method string getNextMaxChildCursor()
 * @method string getNextMinChildCursor()
 * @method int getNumHeadChildComments()
 * @method int getNumTailChildComments()
 * @method User[] getOtherPreviewUsers()
 * @method string getParentCommentId()
 * @method string getPk()
 * @method Comment[] getPreviewChildComments()
 * @method string getStatus()
 * @method string getText()
 * @method int getType()
 * @method User getUser()
 * @method string getUserId()
 * @method bool isBitFlags()
 * @method bool isChildCommentCount()
 * @method bool isCommentLikeCount()
 * @method bool isContentType()
 * @method bool isCreatedAt()
 * @method bool isCreatedAtUtc()
 * @method bool isDidReportAsSpam()
 * @method bool isHasLikedComment()
 * @method bool isHasMoreHeadChildComments()
 * @method bool isHasMoreTailChildComments()
 * @method bool isHasTranslation()
 * @method bool isInlineComposerDisplayCondition()
 * @method bool isMediaId()
 * @method bool isNextMaxChildCursor()
 * @method bool isNextMinChildCursor()
 * @method bool isNumHeadChildComments()
 * @method bool isNumTailChildComments()
 * @method bool isOtherPreviewUsers()
 * @method bool isParentCommentId()
 * @method bool isPk()
 * @method bool isPreviewChildComments()
 * @method bool isStatus()
 * @method bool isText()
 * @method bool isType()
 * @method bool isUser()
 * @method bool isUserId()
 * @method $this setBitFlags(int $value)
 * @method $this setChildCommentCount(int $value)
 * @method $this setCommentLikeCount(int $value)
 * @method $this setContentType(string $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setCreatedAtUtc(string $value)
 * @method $this setDidReportAsSpam(bool $value)
 * @method $this setHasLikedComment(bool $value)
 * @method $this setHasMoreHeadChildComments(bool $value)
 * @method $this setHasMoreTailChildComments(bool $value)
 * @method $this setHasTranslation(bool $value)
 * @method $this setInlineComposerDisplayCondition(string $value)
 * @method $this setMediaId(string $value)
 * @method $this setNextMaxChildCursor(string $value)
 * @method $this setNextMinChildCursor(string $value)
 * @method $this setNumHeadChildComments(int $value)
 * @method $this setNumTailChildComments(int $value)
 * @method $this setOtherPreviewUsers(User[] $value)
 * @method $this setParentCommentId(string $value)
 * @method $this setPk(string $value)
 * @method $this setPreviewChildComments(Comment[] $value)
 * @method $this setStatus(string $value)
 * @method $this setText(string $value)
 * @method $this setType(int $value)
 * @method $this setUser(User $value)
 * @method $this setUserId(string $value)
 * @method $this unsetBitFlags()
 * @method $this unsetChildCommentCount()
 * @method $this unsetCommentLikeCount()
 * @method $this unsetContentType()
 * @method $this unsetCreatedAt()
 * @method $this unsetCreatedAtUtc()
 * @method $this unsetDidReportAsSpam()
 * @method $this unsetHasLikedComment()
 * @method $this unsetHasMoreHeadChildComments()
 * @method $this unsetHasMoreTailChildComments()
 * @method $this unsetHasTranslation()
 * @method $this unsetInlineComposerDisplayCondition()
 * @method $this unsetMediaId()
 * @method $this unsetNextMaxChildCursor()
 * @method $this unsetNextMinChildCursor()
 * @method $this unsetNumHeadChildComments()
 * @method $this unsetNumTailChildComments()
 * @method $this unsetOtherPreviewUsers()
 * @method $this unsetParentCommentId()
 * @method $this unsetPk()
 * @method $this unsetPreviewChildComments()
 * @method $this unsetStatus()
 * @method $this unsetText()
 * @method $this unsetType()
 * @method $this unsetUser()
 * @method $this unsetUserId()
 */
class Comment extends AutoPropertyMapper
{
    /** @var int Top-level comment. */
    const PARENT = 0;
    /** @var int Threaded reply to another comment. */
    const CHILD = 2;

    const JSON_PROPERTY_MAP = [
        'status'                            => 'string',
        'user_id'                           => 'string',
        /*
         * Unix timestamp (UTC) of when the comment was posted.
         * Yes, this is the UTC timestamp even though it's not named "utc"!
         */
        'created_at'                        => 'string',
        /*
         * WARNING: DO NOT USE THIS VALUE! It is NOT a real UTC timestamp.
         * Instagram has messed up their values of "created_at" vs "created_at_utc".
         * In `getComments()`, both have identical values. In `getCommentReplies()`,
         * both are identical too. But in the `getComments()` "reply previews",
         * their "created_at_utc" values are completely wrong (always +8 hours into
         * the future, beyond the real UTC time). So just ignore this bad value!
         * The real app only reads "created_at" for showing comment timestamps!
         */
        'created_at_utc'                    => 'string',
        'bit_flags'                         => 'int',
        'user'                              => 'User',
        'pk'                                => 'string',
        'media_id'                          => 'string',
        'text'                              => 'string',
        'content_type'                      => 'string',
        /*
         * A number describing what type of comment this is. Should be compared
         * against the `Comment::PARENT` and `Comment::CHILD` constants. All
         * replies are of type `CHILD`, and all parents are of type `PARENT`.
         */
        'type'                              => 'int',
        'comment_like_count'                => 'int',
        'has_liked_comment'                 => 'bool',
        'has_translation'                   => 'bool',
        'did_report_as_spam'                => 'bool',
        /*
         * If this is a child in a thread, this is the ID of its parent thread.
         */
        'parent_comment_id'                 => 'string',
        /*
         * Number of child comments in this comment thread.
         */
        'child_comment_count'               => 'int',
        /*
         * Previews of some of the child comments. Compare it to the child
         * comment count. If there are more, you must request the comment thread.
         */
        'preview_child_comments'            => 'Comment[]',
        /*
         * Previews of users in very long comment threads.
         */
        'other_preview_users'               => 'User[]',
        'inline_composer_display_condition' => 'string',
        /*
         * When "has_more_tail_child_comments" is true, you can use the value
         * in "next_max_child_cursor" as "max_id" parameter to load up to
         * "num_tail_child_comments" older child-comments.
         */
        'has_more_tail_child_comments'      => 'bool',
        'next_max_child_cursor'             => 'string',
        'num_tail_child_comments'           => 'int',
        /*
         * When "has_more_head_child_comments" is true, you can use the value
         * in "next_min_child_cursor" as "min_id" parameter to load up to
         * "num_head_child_comments" newer child-comments.
         */
        'has_more_head_child_comments'      => 'bool',
        'next_min_child_cursor'             => 'string',
        'num_head_child_comments'           => 'int',
    ];
}
