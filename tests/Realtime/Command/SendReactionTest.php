<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\SendReaction;
use PHPUnit\Framework\TestCase;

class SendReactionTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendReaction('abc', '123', 'like', 'created');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendReaction('-1', '123', 'like', 'created');
    }

    public function testNonNumericThreadItemIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread item identifier');
        new SendReaction('123', 'abc', 'like', 'created');
    }

    public function testNegativeThreadItemIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread item identifier');
        new SendReaction('123', '-1', 'like', 'created');
    }

    public function testUnknownReactionTypeShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a supported reaction');
        new SendReaction('123', '456', 'angry', 'created');
    }

    public function testUnknownReactionStatusShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a supported reaction status');
        new SendReaction('123', '456', 'like', 'removed');
    }

    public function testCommandOutputWhenLikeIsCreated()
    {
        $command = new SendReaction('123', '456', 'like', 'created');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"reaction","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","reaction_type":"like","reaction_status":"created","item_id":"456","node_type":"item","action":"send_item"}$#',
            json_encode($command)
        );
    }

    public function testCommandOutputWhenLikeIsDeleted()
    {
        $command = new SendReaction('123', '456', 'like', 'deleted');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"reaction","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","reaction_type":"like","reaction_status":"deleted","item_id":"456","node_type":"item","action":"send_item"}$#',
            json_encode($command)
        );
    }

    public function testInvalidClientContextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid UUID');
        new SendReaction('123', '456', 'like', 'created', ['client_context' => 'test']);
    }

    public function testValidClientContextShouldPass()
    {
        $command = new SendReaction('123', '456', 'like', 'created', ['client_context' => 'deadbeef-dead-beef-dead-beefdeadbeef']);
        $this->assertEquals(
            '{"thread_id":"123","item_type":"reaction","client_context":"deadbeef-dead-beef-dead-beefdeadbeef","reaction_type":"like","reaction_status":"created","item_id":"456","node_type":"item","action":"send_item"}',
            json_encode($command)
        );
    }
}
