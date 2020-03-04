<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * MediaInsightsResponse.
 *
 * @method Model\MediaInsights getMediaOrganicInsights()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMediaOrganicInsights()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMediaOrganicInsights(Model\MediaInsights $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMediaOrganicInsights()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class MediaInsightsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'media_organic_insights' => 'Model\MediaInsights',
    ];
}
