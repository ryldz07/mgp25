<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\SendLocation;
use PHPUnit\Framework\TestCase;

class SendLocationTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendLocation('abc', '123456');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendLocation('-1', '123456');
    }

    public function testInvalidLocationIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('not a valid');
        new SendLocation('123', 'location');
    }

    public function testNonStringTextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be a string');
        new SendLocation('123', '123456', [
            'text' => [],
        ]);
    }

    public function testCommandOutput()
    {
        $command = new SendLocation('123', '123456');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"location","text":"","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"123456","action":"send_item","venue_id":"123456"}$#',
            json_encode($command)
        );
    }

    public function testCommandOutputWithText()
    {
        $command = new SendLocation('123', '123456', [
            'text' => 'Text',
        ]);
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"location","text":"Text","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"123456","action":"send_item","venue_id":"123456"}$#',
            json_encode($command)
        );
    }

    public function testInvalidClientContextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid UUID');
        new SendLocation('123', '123456', ['client_context' => 'test']);
    }

    public function testValidClientContextShouldPass()
    {
        $command = new SendLocation('123', '123456', ['client_context' => 'deadbeef-dead-beef-dead-beefdeadbeef']);
        $this->assertEquals(
            '{"thread_id":"123","item_type":"location","text":"","client_context":"deadbeef-dead-beef-dead-beefdeadbeef","item_id":"123456","action":"send_item","venue_id":"123456"}',
            json_encode($command)
        );
    }
}
