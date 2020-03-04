<?php

namespace InstagramAPI\Realtime\Parser;

use Fbns\Client\Thrift\Compact;
use Fbns\Client\Thrift\Reader;
use InstagramAPI\Client;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\ParserInterface;
use InstagramAPI\Realtime\Subscription\GraphQl\AppPresenceSubscription;
use InstagramAPI\Realtime\Subscription\GraphQl\ZeroProvisionSubscription;

class GraphQlParser implements ParserInterface
{
    const FIELD_TOPIC = 1;
    const FIELD_PAYLOAD = 2;

    const TOPIC_DIRECT = 'direct';

    const MODULE_DIRECT = 'direct';

    const TOPIC_TO_MODULE_ENUM = [
        self::TOPIC_DIRECT               => self::MODULE_DIRECT,
        AppPresenceSubscription::QUERY   => AppPresenceSubscription::ID,
        ZeroProvisionSubscription::QUERY => ZeroProvisionSubscription::ID,
    ];

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     * @throws \DomainException
     */
    public function parseMessage(
        $topic,
        $payload)
    {
        $msgTopic = $msgPayload = null;
        new Reader($payload, function ($context, $field, $value, $type) use (&$msgTopic, &$msgPayload) {
            if ($type === Compact::TYPE_BINARY) {
                if ($field === self::FIELD_TOPIC) {
                    $msgTopic = $value;
                } elseif ($field === self::FIELD_PAYLOAD) {
                    $msgPayload = $value;
                }
            }
        });

        return [$this->_createMessage($msgTopic, $msgPayload)];
    }

    /**
     * Create a message from given topic and payload.
     *
     * @param string $topic
     * @param string $payload
     *
     * @throws \RuntimeException
     * @throws \DomainException
     *
     * @return Message
     */
    protected function _createMessage(
        $topic,
        $payload)
    {
        if ($topic === null || $payload === null) {
            throw new \RuntimeException('Incomplete GraphQL message.');
        }

        if (!array_key_exists($topic, self::TOPIC_TO_MODULE_ENUM)) {
            throw new \DomainException(sprintf('Unknown GraphQL topic "%s".', $topic));
        }

        $data = Client::api_body_decode($payload);
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid GraphQL payload.');
        }

        return new Message(self::TOPIC_TO_MODULE_ENUM[$topic], $data);
    }
}
