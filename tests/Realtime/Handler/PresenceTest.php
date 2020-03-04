<?php

namespace InstagramAPI\Tests\Realtime\Handler;

use Evenement\EventEmitterInterface;
use InstagramAPI\Client;
use InstagramAPI\Realtime\Handler\HandlerException;
use InstagramAPI\Realtime\Handler\PresenceHandler;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Response\Model\UserPresence;
use PHPUnit\Framework\TestCase;

class PresenceTest extends TestCase
{
    /**
     * @param $payload
     *
     * @return Message
     */
    protected function _buildMessage(
        $payload)
    {
        return new Message('presence_subscribe', Client::api_body_decode($payload));
    }

    public function testActiveUser()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'presence',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(1, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertInstanceOf(UserPresence::class, $arguments[0]);
                /** @var UserPresence $payload */
                $payload = $arguments[0];
                $this->assertEquals('1111111111', $payload->getUserId());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new PresenceHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"presence_event":{"user_id":"1111111111","is_active":true,"last_activity_at_ms":"123456789012","in_threads":null}}'
        ));
    }

    public function testInvalidData()
    {
        $target = $this->createMock(EventEmitterInterface::class);

        $this->expectException(HandlerException::class);
        $this->expectExceptionMessage('Invalid presence');

        $handler = new PresenceHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{}'
        ));
    }
}
