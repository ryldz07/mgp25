<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * DiscoverPeopleResponse.
 *
 * @method string getMaxId()
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method Model\SuggestedUsers getNewSuggestedUsers()
 * @method string getStatus()
 * @method Model\SuggestedUsers getSuggestedUsers()
 * @method Model\_Message[] get_Messages()
 * @method bool isMaxId()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNewSuggestedUsers()
 * @method bool isStatus()
 * @method bool isSuggestedUsers()
 * @method bool is_Messages()
 * @method $this setMaxId(string $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNewSuggestedUsers(Model\SuggestedUsers $value)
 * @method $this setStatus(string $value)
 * @method $this setSuggestedUsers(Model\SuggestedUsers $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMaxId()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNewSuggestedUsers()
 * @method $this unsetStatus()
 * @method $this unsetSuggestedUsers()
 * @method $this unset_Messages()
 */
class DiscoverPeopleResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'more_available'      => 'bool',
        'max_id'              => 'string',
        'suggested_users'     => 'Model\SuggestedUsers',
        'new_suggested_users' => 'Model\SuggestedUsers',
    ];
}
