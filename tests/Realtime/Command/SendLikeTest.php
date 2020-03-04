<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\SendLike;
use PHPUnit\Framework\TestCase;

class SendLikeTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendLike('abc');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendLike('-1');
    }

    public function testCommandOutput()
    {
        $command = new SendLike('123');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"like","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","action":"send_item"}$#',
            json_encode($command)
        );
    }

    public function testInvalidClientContextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid UUID');
        new SendLike('123', ['client_context' => 'test']);
    }

    public function testValidClientContextShouldPass()
    {
        $command = new SendLike('123', ['client_context' => 'deadbeef-dead-beef-dead-beefdeadbeef']);
        $this->assertEquals(
            '{"thread_id":"123","item_type":"like","client_context":"deadbeef-dead-beef-dead-beefdeadbeef","action":"send_item"}',
            json_encode($command)
        );
    }
}
