<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\Direct\SendHashtag;
use PHPUnit\Framework\TestCase;

class SendHashtagTest extends TestCase
{
    public function testNonNumericThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendHashtag('abc', 'somehashtag');
    }

    public function testNegativeThreadIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid thread identifier');
        new SendHashtag('-1', 'somehashtag');
    }

    public function testNonStringHashtagShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('be a string');
        new SendHashtag('123', 2.5);
    }

    public function testEmptyHashtagShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('not be empty');
        new SendHashtag('123', '');
    }

    public function testNumberSignOnlyShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('not be empty');
        new SendHashtag('123', '#');
    }

    public function testTwoWordsShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be one word');
        new SendHashtag('123', '#cool pics');
    }

    public function testNonStringTextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be a string');
        new SendHashtag('123', 'somehashtag', [
            'text' => [],
        ]);
    }

    public function testCommandOutput()
    {
        $command = new SendHashtag('123', 'somehashtag');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"hashtag","text":"","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"somehashtag","action":"send_item","hashtag":"somehashtag"}$#',
            json_encode($command)
        );
    }

    public function testCommandOutputWithTrim()
    {
        $command = new SendHashtag('123', '#somehashtag              ');
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"hashtag","text":"","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"somehashtag","action":"send_item","hashtag":"somehashtag"}$#',
            json_encode($command)
        );
    }

    public function testCommandOutputWithText()
    {
        $command = new SendHashtag('123', 'somehashtag', [
            'text' => 'Text',
        ]);
        $this->assertRegExp(
            '#^{"thread_id":"123","item_type":"hashtag","text":"Text","client_context":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","item_id":"somehashtag","action":"send_item","hashtag":"somehashtag"}$#',
            json_encode($command)
        );
    }

    public function testInvalidClientContextShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid UUID');
        new SendHashtag('123', 'somehashtag', ['client_context' => 'test']);
    }

    public function testValidClientContextShouldPass()
    {
        $command = new SendHashtag('123', 'somehashtag', ['client_context' => 'deadbeef-dead-beef-dead-beefdeadbeef']);
        $this->assertEquals(
            '{"thread_id":"123","item_type":"hashtag","text":"","client_context":"deadbeef-dead-beef-dead-beefdeadbeef","item_id":"somehashtag","action":"send_item","hashtag":"somehashtag"}',
            json_encode($command)
        );
    }
}
