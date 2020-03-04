<?php

namespace InstagramAPI\Realtime\Parser;

use InstagramAPI\Client;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\ParserInterface;

class IrisParser implements ParserInterface
{
    const MODULE = 'direct';

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function parseMessage(
        $topic,
        $payload)
    {
        $messages = Client::api_body_decode($payload);
        if (!is_array($messages)) {
            throw new \RuntimeException('Invalid Iris payload.');
        }

        $result = [];
        foreach ($messages as $message) {
            $result[] = new Message(self::MODULE, $message);
        }

        return $result;
    }
}
