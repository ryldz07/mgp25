<?php

namespace InstagramAPI\Tests\Realtime\Handler;

use Evenement\EventEmitterInterface;
use InstagramAPI\Client;
use InstagramAPI\Realtime\Handler\DirectHandler;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\Payload\Action\AckAction;
use InstagramAPI\Realtime\Payload\ThreadActivity;
use InstagramAPI\Response\Model\ActionBadge;
use InstagramAPI\Response\Model\DirectSeenItemPayload;
use InstagramAPI\Response\Model\DirectThread;
use InstagramAPI\Response\Model\DirectThreadItem;
use InstagramAPI\Response\Model\DirectThreadLastSeenAt;
use PHPUnit\Framework\TestCase;

class DirectTest extends TestCase
{
    /**
     * @param $payload
     *
     * @return Message
     */
    protected function _buildMessage(
        $payload)
    {
        return new Message('direct', Client::api_body_decode($payload));
    }

    public function testThreadItemCreation()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        // listeners() call is at index 0.
        $target->expects($this->at(1))->method('emit')->with(
            'thread-item-created',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(3, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('111111111111111111111111111111111111111', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertEquals('11111111111111111111111111111111111', $arguments[1]);
                $this->assertArrayHasKey(2, $arguments);
                $this->assertInstanceOf(DirectThreadItem::class, $arguments[2]);
                /** @var DirectThreadItem $item */
                $item = $arguments[2];
                $this->assertEquals('TEXT', $item->getText());

                return true;
            })
        );
        // listeners() call is at index 2.
        $target->expects($this->at(3))->method('emit')->with(
            'unseen-count-update',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(2, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('inbox', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertInstanceOf(DirectSeenItemPayload::class, $arguments[1]);
                /** @var DirectSeenItemPayload $payload */
                $payload = $arguments[1];
                $this->assertEquals(1, $payload->getCount());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"add","path":"/direct_v2/threads/111111111111111111111111111111111111111/items/11111111111111111111111111111111111","value":"{\"item_id\": \"11111111111111111111111111111111111\", \"user_id\": 1111111111, \"timestamp\": 1111111111111111, \"item_type\": \"text\", \"text\": \"TEXT\"}","doublePublish":true},{"op":"replace","path":"/direct_v2/inbox/unseen_count","value":"1","ts":"1111111111111111","doublePublish":true}],"lazy":false,"publish_metadata":{"topic_publish_id":1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":1}'
        ));
    }

    public function testThreadUpdate()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'thread-updated',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(2, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('111111111111111111111111111111111111111', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertInstanceOf(DirectThread::class, $arguments[1]);
                /** @var DirectThread $thread */
                $thread = $arguments[1];
                $this->assertEquals('111111111111111111111111111111111111111', $thread->getThreadId());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"replace","path":"/direct_v2/inbox/threads/111111111111111111111111111111111111111","value":"{\"thread_id\": \"111111111111111111111111111111111111111\"}","doublePublish":true}],"publish_metadata":{"topic_publish_id":1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":1}'
        ));
    }

    public function testThreadItemRemoval()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'thread-item-removed',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(2, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('111111111111111111111111111111111111111', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertEquals('11111111111111111111111111111111111', $arguments[1]);

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"remove","path":"/direct_v2/threads/111111111111111111111111111111111111111/items/11111111111111111111111111111111111","value":"11111111111111111111111111111111111","doublePublish":true}],"lazy":false,"publish_metadata":{"topic_publish_id":1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":1}'
        ));
    }

    public function testThreadActivity()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'thread-activity',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(2, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('111111111111111111111111111111111111111', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertInstanceOf(ThreadActivity::class, $arguments[1]);
                /** @var ThreadActivity $activity */
                $activity = $arguments[1];
                $this->assertEquals(1, $activity->getActivityStatus());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"add","path":"/direct_v2/threads/111111111111111111111111111111111111111/activity_indicator_id/deadbeef-dead-beef-dead-beefdeadbeef","value":"{\"timestamp\": 1111111111111111, \"sender_id\": \"1111111111\", \"ttl\": 12000, \"activity_status\": 1}","doublePublish":true}],"lazy":false,"publish_metadata":{"topic_publish_id":-1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":1}'
        ));
    }

    public function testThreadHasSeen()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'thread-seen',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(3, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('111111111111111111111111111111111111111', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertEquals('1111111111', $arguments[1]);
                $this->assertArrayHasKey(2, $arguments);
                $this->assertInstanceOf(DirectThreadLastSeenAt::class, $arguments[2]);
                /** @var DirectThreadLastSeenAt $lastSeen */
                $lastSeen = $arguments[2];
                $this->assertEquals('11111111111111111111111111111111111', $lastSeen->getItemId());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"replace","path":"/direct_v2/threads/111111111111111111111111111111111111111/participants/1111111111/has_seen","value":"{\"timestamp\": 1111111111111111, \"item_id\": 11111111111111111111111111111111111}","doublePublish":true}],"lazy":false,"publish_metadata":{"topic_publish_id":-1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":1}'
        ));
    }

    public function testThreadActionBadge()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'direct-story-action',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(2, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('111111111111111111111111111111111111111', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertInstanceOf(ActionBadge::class, $arguments[1]);
                /** @var ActionBadge $actionBadge */
                $actionBadge = $arguments[1];
                $this->assertEquals('raven_delivered', $actionBadge->getActionType());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"replace","path":"/direct_v2/visual_action_badge/111111111111111111111111111111111111111","value":"{\"action_type\": \"raven_delivered\", \"action_count\": 1, \"action_timestamp\": 1111111111111111}","doublePublish":true}],"lazy":false,"publish_metadata":{"topic_publish_id":-1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":3}'
        ));
    }

    public function testStoryCreation()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        // listeners() call is at index 0.
        $target->expects($this->at(1))->method('emit')->with(
            'direct-story-updated',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(3, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('111111111111111111111111111111111111111', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertEquals('11111111111111111111111111111111111', $arguments[1]);
                $this->assertArrayHasKey(2, $arguments);
                $this->assertInstanceOf(DirectThreadItem::class, $arguments[2]);
                /** @var DirectThreadItem $item */
                $item = $arguments[2];
                $this->assertEquals('raven_media', $item->getItemType());

                return true;
            })
        );
        // listeners() call is at index 2.
        $target->expects($this->at(3))->method('emit')->with(
            'unseen-count-update',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(2, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('visual_inbox', $arguments[0]);
                $this->assertArrayHasKey(1, $arguments);
                $this->assertInstanceOf(DirectSeenItemPayload::class, $arguments[1]);
                /** @var DirectSeenItemPayload $payload */
                $payload = $arguments[1];
                $this->assertEquals(1, $payload->getCount());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"event":"patch","data":[{"op":"add","path":"/direct_v2/visual_threads/111111111111111111111111111111111111111/items/11111111111111111111111111111111111","value":"{\"item_id\": \"11111111111111111111111111111111111\", \"user_id\": 1111111111, \"timestamp\": 1111111111111111, \"item_type\": \"raven_media\", \"seen_user_ids\": [], \"reply_chain_count\": 0, \"view_mode\": \"once\"}","doublePublish":true},{"op":"replace","path":"/direct_v2/visual_inbox/unseen_count","value":"1","ts":"1111111111111111","doublePublish":true}],"version":"9.6.0","publish_metadata":{"topic_publish_id":-1111111111111111111,"publish_time_ms":"1970-01-01 00:00:00.000000"},"num_endpoints":3}'
        ));
    }

    public function testSendItemAck()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'client-context-ack',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(1, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertInstanceOf(AckAction::class, $arguments[0]);
                /** @var AckAction $ack */
                $ack = $arguments[0];
                $this->assertEquals('11111111111111111111111111111111111', $ack->getPayload()->getItemId());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"action": "item_ack", "status_code": "200", "payload": {"client_context": "deadbeef-dead-beef-dead-beefdeadbeef", "item_id": "11111111111111111111111111111111111", "timestamp": "1111111111111111", "thread_id": "111111111111111111111111111111111111111"}, "status": "ok"}'
        ));
    }

    public function testActivityAck()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'client-context-ack',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(1, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertInstanceOf(AckAction::class, $arguments[0]);
                /** @var AckAction $ack */
                $ack = $arguments[0];
                $this->assertEquals('deadbeef-dead-beef-dead-beefdeadbeef', $ack->getPayload()->getClientContext());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new DirectHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"action": "item_ack", "status_code": "200", "payload": {"activity_status": 1, "indicate_activity_ts": 1111111111111111, "client_context": "deadbeef-dead-beef-dead-beefdeadbeef", "ttl": 10000}, "status": "ok"}'
        ));
    }
}
