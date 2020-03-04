<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * LiveViewerInvite.
 *
 * @method Broadcast getBroadcast()
 * @method string getMessage()
 * @method string getText()
 * @method string getTitle()
 * @method bool isBroadcast()
 * @method bool isMessage()
 * @method bool isText()
 * @method bool isTitle()
 * @method $this setBroadcast(Broadcast $value)
 * @method $this setMessage(string $value)
 * @method $this setText(string $value)
 * @method $this setTitle(string $value)
 * @method $this unsetBroadcast()
 * @method $this unsetMessage()
 * @method $this unsetText()
 * @method $this unsetTitle()
 */
class LiveViewerInvite extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'text'      => 'string',
        'broadcast' => 'Broadcast',
        'title'     => 'string',
        'message'   => 'string',
    ];
}
