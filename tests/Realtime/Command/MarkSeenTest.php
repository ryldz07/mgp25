<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\MarkSeen;
use PHPUnit\Framework\TestCase;

class MarkSeenTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new MarkSeen('abc', '123');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new MarkSeen('-1', '123');
    }

    public function testNonNumericThreadItemIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread item identifier');
        new MarkSeen('123', 'abc');
    }

    public function testNegativeThreadItemIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread item identifier');
        new MarkSeen('123', '-1');
    }

    public function testCommandOutput()
    {
        $command = new MarkSeen('123', '456');
        $this->assertEquals(
            '{"thread_id":"123","item_id":"456","action":"mark_seen"}',
            json_encode($command)
        );
    }

    public function testCustomClientContextIsIgnored()
    {
        $command = new MarkSeen('123', '456', ['client_context' => 'test']);
        $this->assertEquals(
            '{"thread_id":"123","item_id":"456","action":"mark_seen"}',
            json_encode($command)
        );
    }
}
