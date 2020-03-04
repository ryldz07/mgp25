<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * DirectReaction.
 *
 * @method string getClientContext()
 * @method string getItemId()
 * @method string getNodeType()
 * @method string getReactionStatus()
 * @method string getReactionType()
 * @method string getSenderId()
 * @method string getTimestamp()
 * @method bool isClientContext()
 * @method bool isItemId()
 * @method bool isNodeType()
 * @method bool isReactionStatus()
 * @method bool isReactionType()
 * @method bool isSenderId()
 * @method bool isTimestamp()
 * @method $this setClientContext(string $value)
 * @method $this setItemId(string $value)
 * @method $this setNodeType(string $value)
 * @method $this setReactionStatus(string $value)
 * @method $this setReactionType(string $value)
 * @method $this setSenderId(string $value)
 * @method $this setTimestamp(string $value)
 * @method $this unsetClientContext()
 * @method $this unsetItemId()
 * @method $this unsetNodeType()
 * @method $this unsetReactionStatus()
 * @method $this unsetReactionType()
 * @method $this unsetSenderId()
 * @method $this unsetTimestamp()
 */
class DirectReaction extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'reaction_type'   => 'string',
        'timestamp'       => 'string',
        'sender_id'       => 'string',
        'client_context'  => 'string',
        'reaction_status' => 'string',
        'node_type'       => 'string',
        'item_id'         => 'string',
    ];
}
