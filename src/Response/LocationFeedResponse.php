<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * LocationFeedResponse.
 *
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method string getNextMaxId()
 * @method int[] getNextMediaIds()
 * @method int getNextPage()
 * @method Model\Section[] getSections()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNextMediaIds()
 * @method bool isNextPage()
 * @method bool isSections()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNextMediaIds(int[] $value)
 * @method $this setNextPage(int $value)
 * @method $this setSections(Model\Section[] $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetNextMediaIds()
 * @method $this unsetNextPage()
 * @method $this unsetSections()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class LocationFeedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'sections'               => 'Model\Section[]',
        'next_page'              => 'int',
        'more_available'         => 'bool',
        'next_media_ids'         => 'int[]',
        'next_max_id'            => 'string',
    ];
}
