<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TVGuideResponse.
 *
 * @method Model\Badging getBadging()
 * @method Model\TVChannel[] getChannels()
 * @method Model\Composer getComposer()
 * @method mixed getMessage()
 * @method Model\TVChannel getMyChannel()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isBadging()
 * @method bool isChannels()
 * @method bool isComposer()
 * @method bool isMessage()
 * @method bool isMyChannel()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setBadging(Model\Badging $value)
 * @method $this setChannels(Model\TVChannel[] $value)
 * @method $this setComposer(Model\Composer $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMyChannel(Model\TVChannel $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetBadging()
 * @method $this unsetChannels()
 * @method $this unsetComposer()
 * @method $this unsetMessage()
 * @method $this unsetMyChannel()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class TVGuideResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'channels'   => 'Model\TVChannel[]',
        'my_channel' => 'Model\TVChannel',
        'badging'    => 'Model\Badging',
        'composer'   => 'Model\Composer',
    ];
}
