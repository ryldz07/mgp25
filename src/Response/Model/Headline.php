<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Headline.
 *
 * @method int getBitFlags()
 * @method mixed getContentType()
 * @method string getCreatedAt()
 * @method string getCreatedAtUtc()
 * @method string getMediaId()
 * @method string getPk()
 * @method mixed getStatus()
 * @method string getText()
 * @method mixed getType()
 * @method User getUser()
 * @method string getUserId()
 * @method bool isBitFlags()
 * @method bool isContentType()
 * @method bool isCreatedAt()
 * @method bool isCreatedAtUtc()
 * @method bool isMediaId()
 * @method bool isPk()
 * @method bool isStatus()
 * @method bool isText()
 * @method bool isType()
 * @method bool isUser()
 * @method bool isUserId()
 * @method $this setBitFlags(int $value)
 * @method $this setContentType(mixed $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setCreatedAtUtc(string $value)
 * @method $this setMediaId(string $value)
 * @method $this setPk(string $value)
 * @method $this setStatus(mixed $value)
 * @method $this setText(string $value)
 * @method $this setType(mixed $value)
 * @method $this setUser(User $value)
 * @method $this setUserId(string $value)
 * @method $this unsetBitFlags()
 * @method $this unsetContentType()
 * @method $this unsetCreatedAt()
 * @method $this unsetCreatedAtUtc()
 * @method $this unsetMediaId()
 * @method $this unsetPk()
 * @method $this unsetStatus()
 * @method $this unsetText()
 * @method $this unsetType()
 * @method $this unsetUser()
 * @method $this unsetUserId()
 */
class Headline extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'content_type'   => '',
        'user'           => 'User',
        'user_id'        => 'string',
        'pk'             => 'string',
        'text'           => 'string',
        'type'           => '',
        'created_at'     => 'string',
        'created_at_utc' => 'string',
        'media_id'       => 'string',
        'bit_flags'      => 'int',
        'status'         => '',
    ];
}
