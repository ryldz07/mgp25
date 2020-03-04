<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\SendText;
use PHPUnit\Framework\TestCase;

class SendTextTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendText('abc', 'test');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendText('-1', 'test');
    }

    public function testEmptyTextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be empty');
        new SendText('123', '');
    }

    public function testNonStringTextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be a string');
        new SendText('123', null);
    }

    public function testCommandOutput()
    {
        $command = new SendText('123', 'test');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"text","text":"test","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","action":"send_item"}$#',
            json_encode($command)
        );
    }

    public function testInvalidClientContextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid UUID');
        new SendText('123', 'test', ['client_context' => 'test']);
    }

    public function testValidClientContextShouldPass()
    {
        $command = new SendText('123', 'test', ['client_context' => 'deadbeef-dead-beef-dead-beefdeadbeef']);
        $this->assertEquals(
            '{"thread_id":"123","item_type":"text","text":"test","client_context":"deadbeef-dead-beef-dead-beefdeadbeef","action":"send_item"}',
            json_encode($command)
        );
    }
}
