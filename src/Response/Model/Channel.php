<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Channel.
 *
 * @method string getChannelId()
 * @method mixed getChannelType()
 * @method mixed getContext()
 * @method mixed getHeader()
 * @method Item getMedia()
 * @method int getMediaCount()
 * @method mixed getTitle()
 * @method bool isChannelId()
 * @method bool isChannelType()
 * @method bool isContext()
 * @method bool isHeader()
 * @method bool isMedia()
 * @method bool isMediaCount()
 * @method bool isTitle()
 * @method $this setChannelId(string $value)
 * @method $this setChannelType(mixed $value)
 * @method $this setContext(mixed $value)
 * @method $this setHeader(mixed $value)
 * @method $this setMedia(Item $value)
 * @method $this setMediaCount(int $value)
 * @method $this setTitle(mixed $value)
 * @method $this unsetChannelId()
 * @method $this unsetChannelType()
 * @method $this unsetContext()
 * @method $this unsetHeader()
 * @method $this unsetMedia()
 * @method $this unsetMediaCount()
 * @method $this unsetTitle()
 */
class Channel extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'channel_id'   => 'string',
        'channel_type' => '',
        'title'        => '',
        'header'       => '',
        'media_count'  => 'int',
        'media'        => 'Item',
        'context'      => '',
    ];
}
