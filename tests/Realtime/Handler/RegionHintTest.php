<?php

namespace InstagramAPI\Tests\Realtime\Handler;

use Evenement\EventEmitterInterface;
use InstagramAPI\Realtime\Handler\HandlerException;
use InstagramAPI\Realtime\Handler\RegionHintHandler;
use InstagramAPI\Realtime\Message;
use PHPUnit\Framework\TestCase;

class RegionHintTest extends TestCase
{
    /**
     * @param $payload
     *
     * @return Message
     */
    protected function _buildMessage(
        $payload)
    {
        return new Message('region_hint', $payload);
    }

    public function testValidRegionHint()
    {
        $target = $this->createMock(EventEmitterInterface::class);
        $target->expects($this->once())->method('emit')->with(
            'region-hint',
            $this->callback(function ($arguments) {
                $this->assertInternalType('array', $arguments);
                $this->assertCount(1, $arguments);
                $this->assertArrayHasKey(0, $arguments);
                $this->assertEquals('ASH', $arguments[0]);

                return true;
            })
        );
        $target->expects($this->any())->method('listeners')->willReturn([1]);

        $handler = new RegionHintHandler($target);
        $handler->handleMessage($this->_buildMessage(
            'ASH'
        ));
    }

    public function testEmptyRegionHint()
    {
        $target = $this->createMock(EventEmitterInterface::class);

        $this->expectException(HandlerException::class);
        $this->expectExceptionMessage('Invalid region hint');

        $handler = new RegionHintHandler($target);
        $handler->handleMessage($this->_buildMessage(
            ''
        ));
    }

    public function testNullRegionHint()
    {
        $target = $this->createMock(EventEmitterInterface::class);

        $this->expectException(HandlerException::class);
        $this->expectExceptionMessage('Invalid region hint');

        $handler = new RegionHintHandler($target);
        $handler->handleMessage($this->_buildMessage(
            null
        ));
    }
}
