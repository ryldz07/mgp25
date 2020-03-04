<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\SendPost;
use PHPUnit\Framework\TestCase;

class SendPostTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendPost('abc', '123_456');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendPost('-1', '123_456');
    }

    public function testInvalidMediaIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('not a valid');
        new SendPost('123', 'abc_def');
    }

    public function testNonStringTextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be a string');
        new SendPost('123', '123_456', [
            'text' => [],
        ]);
    }

    public function testCommandOutput()
    {
        $command = new SendPost('123', '123_456');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"media_share","text":"","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","action":"send_item","media_id":"123_456"}$#',
            json_encode($command)
        );
    }

    public function testCommandOutputWithText()
    {
        $command = new SendPost('123', '123_456', [
            'text' => 'Text',
        ]);
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"media_share","text":"Text","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","action":"send_item","media_id":"123_456"}$#',
            json_encode($command)
        );
    }

    public function testInvalidClientContextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid UUID');
        new SendPost('123', '123_456', ['client_context' => 'test']);
    }

    public function testValidClientContextShouldPass()
    {
        $command = new SendPost('123', '123_456', ['client_context' => 'deadbeef-dead-beef-dead-beefdeadbeef']);
        $this->assertEquals(
            '{"thread_id":"123","item_type":"media_share","text":"","client_context":"deadbeef-dead-beef-dead-beefdeadbeef","action":"send_item","media_id":"123_456"}',
            json_encode($command)
        );
    }
}
