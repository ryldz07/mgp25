<?php

namespace InstagramAPI\Tests\Realtime\Handler;

use Evenement\EventEmitterInterface;
use InstagramAPI\Client;
use InstagramAPI\Realtime\Handler\HandlerException;
use InstagramAPI\Realtime\Handler\IrisHandler;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\Payload\IrisSubscribeAck;
use PHPUnit\Framework\TestCase;

class IrisHandlerTest extends TestCase
{
    /**
     * @param $payload
     *
     * @return Message
     */
    protected function _buildMessage(
        $payload)
    {
        return new Message('iris', Client::api_body_decode($payload));
    }

    public function testSucessfullSubscribe()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'iris-subscribed',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(1, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertInstanceOf(IrisSubscribeAck::class, $arguments[0]);
                /** @var IrisSubscribeAck $ack */
                $ack = $arguments[0];
                $this->assertTrue($ack->getSucceeded());
                $this->assertEquals(666, $ack->getSeqId());

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new IrisHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"succeeded":true,"seq_id":666,"error_type":null,"error_message":null,"subscribed_at_ms":1111111111111}'
        ));
    }

    public function testQueueOverlow()
    {
        $target = $this->createMock(EventEmitterInterface::class);

        $this->expectException(HandlerException::class);
        $this->expectExceptionMessage('IrisQueueOverflowException');

        $handler = new IrisHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"succeeded":false,"seq_id":null,"error_type":1,"error_message":"IrisQueueOverflowException","subscribed_at_ms":null}'
        ));
    }

    public function testQueueUnderflow()
    {
        $target = $this->createMock(EventEmitterInterface::class);

        $this->expectException(HandlerException::class);
        $this->expectExceptionMessage('IrisQueueUnderflowException');

        $handler = new IrisHandler($target);
        $handler->handleMessage($this->_buildMessage(
            '{"succeeded":false,"seq_id":null,"error_type":1,"error_message":"IrisQueueUnderflowException","subscribed_at_ms":null}'
        ));
    }
}
