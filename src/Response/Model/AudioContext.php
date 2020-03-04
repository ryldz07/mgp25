<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * AudioContext.
 *
 * @method string getAudioSrc()
 * @method int getDuration()
 * @method bool isAudioSrc()
 * @method bool isDuration()
 * @method $this setAudioSrc(string $value)
 * @method $this setDuration(int $value)
 * @method $this unsetAudioSrc()
 * @method $this unsetDuration()
 */
class AudioContext extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'audio_src'                            => 'string',
        'duration'                             => 'int',
    ];
}
