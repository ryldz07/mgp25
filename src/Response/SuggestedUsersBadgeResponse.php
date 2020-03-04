<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SuggestedUsersBadgeResponse.
 *
 * @method mixed getMessage()
 * @method string[] getNewSuggestionIds()
 * @method mixed getShouldBadge()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isNewSuggestionIds()
 * @method bool isShouldBadge()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setNewSuggestionIds(string[] $value)
 * @method $this setShouldBadge(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetNewSuggestionIds()
 * @method $this unsetShouldBadge()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class SuggestedUsersBadgeResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'should_badge'       => '',
        'new_suggestion_ids' => 'string[]',
    ];
}
