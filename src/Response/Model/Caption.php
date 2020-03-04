<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Caption.
 *
 * @method int getBitFlags()
 * @method mixed getContentType()
 * @method string getCreatedAt()
 * @method string getCreatedAtUtc()
 * @method bool getDidReportAsSpam()
 * @method bool getHasTranslation()
 * @method string getMediaId()
 * @method string getPk()
 * @method bool getShareEnabled()
 * @method mixed getStatus()
 * @method string getText()
 * @method int getType()
 * @method User getUser()
 * @method string getUserId()
 * @method bool isBitFlags()
 * @method bool isContentType()
 * @method bool isCreatedAt()
 * @method bool isCreatedAtUtc()
 * @method bool isDidReportAsSpam()
 * @method bool isHasTranslation()
 * @method bool isMediaId()
 * @method bool isPk()
 * @method bool isShareEnabled()
 * @method bool isStatus()
 * @method bool isText()
 * @method bool isType()
 * @method bool isUser()
 * @method bool isUserId()
 * @method $this setBitFlags(int $value)
 * @method $this setContentType(mixed $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setCreatedAtUtc(string $value)
 * @method $this setDidReportAsSpam(bool $value)
 * @method $this setHasTranslation(bool $value)
 * @method $this setMediaId(string $value)
 * @method $this setPk(string $value)
 * @method $this setShareEnabled(bool $value)
 * @method $this setStatus(mixed $value)
 * @method $this setText(string $value)
 * @method $this setType(int $value)
 * @method $this setUser(User $value)
 * @method $this setUserId(string $value)
 * @method $this unsetBitFlags()
 * @method $this unsetContentType()
 * @method $this unsetCreatedAt()
 * @method $this unsetCreatedAtUtc()
 * @method $this unsetDidReportAsSpam()
 * @method $this unsetHasTranslation()
 * @method $this unsetMediaId()
 * @method $this unsetPk()
 * @method $this unsetShareEnabled()
 * @method $this unsetStatus()
 * @method $this unsetText()
 * @method $this unsetType()
 * @method $this unsetUser()
 * @method $this unsetUserId()
 */
class Caption extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'status'             => '',
        'user_id'            => 'string',
        'created_at_utc'     => 'string',
        'created_at'         => 'string',
        'bit_flags'          => 'int',
        'user'               => 'User',
        'content_type'       => '',
        'text'               => 'string',
        'media_id'           => 'string',
        'pk'                 => 'string',
        'type'               => 'int',
        'has_translation'    => 'bool',
        'did_report_as_spam' => 'bool',
        'share_enabled'      => 'bool',
    ];
}
