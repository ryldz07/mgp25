<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * PostLiveCommentsResponse.
 *
 * @method Model\LiveComment[] getComments()
 * @method mixed getEndingOffset()
 * @method mixed getMessage()
 * @method mixed getNextFetchOffset()
 * @method Model\LiveComment[] getPinnedComments()
 * @method mixed getStartingOffset()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isComments()
 * @method bool isEndingOffset()
 * @method bool isMessage()
 * @method bool isNextFetchOffset()
 * @method bool isPinnedComments()
 * @method bool isStartingOffset()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setComments(Model\LiveComment[] $value)
 * @method $this setEndingOffset(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNextFetchOffset(mixed $value)
 * @method $this setPinnedComments(Model\LiveComment[] $value)
 * @method $this setStartingOffset(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetComments()
 * @method $this unsetEndingOffset()
 * @method $this unsetMessage()
 * @method $this unsetNextFetchOffset()
 * @method $this unsetPinnedComments()
 * @method $this unsetStartingOffset()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class PostLiveCommentsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'starting_offset'   => '',
        'ending_offset'     => '',
        'next_fetch_offset' => '',
        'comments'          => 'Model\LiveComment[]',
        'pinned_comments'   => 'Model\LiveComment[]',
    ];
}
