<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * CreateCollectionResponse.
 *
 * @method string getCollectionId()
 * @method string getCollectionName()
 * @method Model\Item getCoverMedia()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isCollectionId()
 * @method bool isCollectionName()
 * @method bool isCoverMedia()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setCollectionId(string $value)
 * @method $this setCollectionName(string $value)
 * @method $this setCoverMedia(Model\Item $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetCollectionId()
 * @method $this unsetCollectionName()
 * @method $this unsetCoverMedia()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class CreateCollectionResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        Model\Collection::class, // Import property map.
    ];
}
