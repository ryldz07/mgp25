<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * DirectRankedRecipientsResponse.
 *
 * @method mixed getExpires()
 * @method mixed getFiltered()
 * @method mixed getMessage()
 * @method string getRankToken()
 * @method Model\DirectRankedRecipient[] getRankedRecipients()
 * @method string getRequestId()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isExpires()
 * @method bool isFiltered()
 * @method bool isMessage()
 * @method bool isRankToken()
 * @method bool isRankedRecipients()
 * @method bool isRequestId()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setExpires(mixed $value)
 * @method $this setFiltered(mixed $value)
 * @method $this setMessage(mixed $value)
 * @method $this setRankToken(string $value)
 * @method $this setRankedRecipients(Model\DirectRankedRecipient[] $value)
 * @method $this setRequestId(string $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetExpires()
 * @method $this unsetFiltered()
 * @method $this unsetMessage()
 * @method $this unsetRankToken()
 * @method $this unsetRankedRecipients()
 * @method $this unsetRequestId()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class DirectRankedRecipientsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'expires'           => '',
        'ranked_recipients' => 'Model\DirectRankedRecipient[]',
        'filtered'          => '',
        'request_id'        => 'string',
        'rank_token'        => 'string',
    ];
}
