<?php

namespace InstagramAPI\Realtime\Parser;

use InstagramAPI\Client;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\ParserInterface;

class JsonParser implements ParserInterface
{
    /** @var string */
    protected $_module;

    /**
     * Constructor.
     *
     * @param string $module
     */
    public function __construct(
        $module)
    {
        $this->_module = $module;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function parseMessage(
        $topic,
        $payload)
    {
        $data = Client::api_body_decode($payload);
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid JSON payload.');
        }

        return [new Message($this->_module, $data)];
    }
}
