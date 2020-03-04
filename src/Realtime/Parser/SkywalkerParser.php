<?php

namespace InstagramAPI\Realtime\Parser;

use Fbns\Client\Thrift\Compact;
use Fbns\Client\Thrift\Reader;
use InstagramAPI\Client;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\ParserInterface;

class SkywalkerParser implements ParserInterface
{
    const FIELD_TOPIC = 1;
    const FIELD_PAYLOAD = 2;

    const TOPIC_DIRECT = 1;
    const TOPIC_LIVE = 2;
    const TOPIC_LIVEWITH = 3;

    const MODULE_DIRECT = 'direct';
    const MODULE_LIVE = 'live';
    const MODULE_LIVEWITH = 'livewith';

    const TOPIC_TO_MODULE_ENUM = [
        self::TOPIC_DIRECT   => self::MODULE_DIRECT,
        self::TOPIC_LIVE     => self::MODULE_LIVE,
        self::TOPIC_LIVEWITH => self::MODULE_LIVEWITH,
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
            if ($type === Compact::TYPE_I32 && $field === self::FIELD_TOPIC) {
                $msgTopic = $value;
            } elseif ($type === Compact::TYPE_BINARY && $field === self::FIELD_PAYLOAD) {
                $msgPayload = $value;
            }
        });

        return [$this->_createMessage($msgTopic, $msgPayload)];
    }

    /**
     * Create a message from given topic and payload.
     *
     * @param int    $topic
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
            throw new \RuntimeException('Incomplete Skywalker message.');
        }

        if (!array_key_exists($topic, self::TOPIC_TO_MODULE_ENUM)) {
            throw new \DomainException(sprintf('Unknown Skywalker topic "%d".', $topic));
        }

        $data = Client::api_body_decode($payload);
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid Skywalker payload.');
        }

        return new Message(self::TOPIC_TO_MODULE_ENUM[$topic], $data);
    }
}
