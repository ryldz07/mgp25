<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * LocationStoryResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\StoryTray getStory()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isStory()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setStory(Model\StoryTray $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetStory()
 * @method $this unset_Messages()
 */
class LocationStoryResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'story'               => 'Model\StoryTray',
    ];
}
