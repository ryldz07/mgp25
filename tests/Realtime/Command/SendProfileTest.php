<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\SendProfile;
use PHPUnit\Framework\TestCase;

class SendProfileTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendProfile('abc', '123456');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendProfile('-1', '123456');
    }

    public function testInvalidUserIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('not a valid');
        new SendProfile('123', 'username');
    }

    public function testNonStringTextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be a string');
        new SendProfile('123', '123456', [
            'text' => [],
        ]);
    }

    public function testCommandOutput()
    {
        $command = new SendProfile('123', '123456');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"profile","text":"","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"123456","action":"send_item","profile_user_id":"123456"}$#',
            json_encode($command)
        );
    }

    public function testCommandOutputWithText()
    {
        $command = new SendProfile('123', '123456', [
            'text' => 'Text',
        ]);
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"profile","text":"Text","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"123456","action":"send_item","profile_user_id":"123456"}$#',
            json_encode($command)
        );
    }

    public function testInvalidClientContextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid UUID');
        new SendProfile('123', '123456', ['client_context' => 'test']);
    }

    public function testValidClientContextShouldPass()
    {
        $command = new SendProfile('123', '123456', ['client_context' => 'deadbeef-dead-beef-dead-beefdeadbeef']);
        $this->assertEquals(
            '{"thread_id":"123","item_type":"profile","text":"","client_context":"deadbeef-dead-beef-dead-beefdeadbeef","item_id":"123456","action":"send_item","profile_user_id":"123456"}',
            json_encode($command)
        );
    }
}
