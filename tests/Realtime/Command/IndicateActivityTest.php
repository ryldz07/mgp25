<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\IndicateActivity;
use PHPUnit\Framework\TestCase;

class IndicateActivityTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new IndicateActivity('abc', '123');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new IndicateActivity('-1', '123');
    }

    public function testCommandOutputWhenTrue()
    {
        $command = new IndicateActivity('123', true);
        $this->assertRegExp(
            '#^{"thread_id":"123","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","activity_status":"1","action":"indicate_activity"}$#',
            json_encode($command)
        );
    }

    public function testCommandOutputWhenFalse()
    {
        $command = new IndicateActivity('123', false);
        $this->assertRegExp(
            '#^{"thread_id":"123","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","activity_status":"0","action":"indicate_activity"}$#',
            json_encode($command)
        );
    }

    public function testInvalidClientContextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid UUID');
        new IndicateActivity('123', true, ['client_context' => 'test']);
    }

    public function testValidClientContextShouldPass()
    {
        $command = new IndicateActivity('123', true, ['client_context' => 'deadbeef-dead-beef-dead-beefdeadbeef']);
        $this->assertEquals(
            '{"thread_id":"123","client_context":"deadbeef-dead-beef-dead-beefdeadbeef","activity_status":"1","action":"indicate_activity"}',
            json_encode($command)
        );
    }
}
