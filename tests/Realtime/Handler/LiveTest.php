<?php

namespace InstagramAPI\Tests\Realtime\Handler;

use Evenement\EventEmitterInterface;
use InstagramAPI\Client;
use InstagramAPI\Realtime\Handler\LiveHandler;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\Payload\LiveBroadcast;
use PHPUnit\Framework\TestCase;

class LiveTest extends TestCase
{
    /**
     * @param $payload
     *
     * @return Message
     */
    protected function _buildMessage(
        $payload)
    {
        return new Message('live', Client::api_body_decode($payload));
    }

    public function testStartBroadcast()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'live-started',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(1, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertInstanceOf(LiveBroadcast::class, $arguments[0]);
                /** @var LiveBroadcast $live */
                $live = $arguments[0];
                $this->assertEquals('11111111111111111', $live->getBroadcastId());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new LiveHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"add","path":"/broadcast/11111111111111111/started","value":"{\"broadcast_id\": 11111111111111111, \"user\": {\"pk\": 1111111111, \"username\": \"USERNAME\", \"full_name\": \"\", \"is_private\": true, \"profile_pic_url\": \"\", \"profile_pic_id\": \"\", \"is_verified\": false}, \"published_time\": 1234567890, \"is_periodic\": 0, \"broadcast_message\": \"\", \"display_notification\": true}","doublePublish":true}],"lazy":false,"publisher":1111111111,"version":"9.7.0","publish_metadata":{"topic_publish_id":1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":2}'
        ));
    }

    public function testStopBroadcast()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'live-stopped',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(1, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertInstanceOf(LiveBroadcast::class, $arguments[0]);
                /** @var LiveBroadcast $live */
                $live = $arguments[0];
                $this->assertEquals('11111111111111111', $live->getBroadcastId());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new LiveHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"remove","path":"/broadcast/11111111111111111/ended","value":"{\"broadcast_id\": 11111111111111111, \"user\": {\"pk\": 1111111111, \"username\": \"USERNAME\", \"full_name\": \"NAME\", \"is_private\": true, \"profile_pic_url\": \"\", \"profile_pic_id\": \"\", \"is_verified\": false}, \"published_time\": 1234567890, \"is_periodic\": 0, \"broadcast_message\": \"\", \"display_notification\": false}","doublePublish":true}],"lazy":false,"publisher":1111111111,"version":"10.8.0","publish_metadata":{"topic_publish_id":1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":1}'
        ));
    }
}
