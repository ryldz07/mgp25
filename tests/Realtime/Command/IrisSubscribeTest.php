<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\IrisSubscribe;
use PHPUnit\Framework\TestCase;

class IrisSubscribeTest extends TestCase
{
    public function testInvalidSequenceIdShouldThrow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Iris sequence identifier');
        new IrisSubscribe(-1);
    }

    public function testCommandOutput()
    {
        $command = new IrisSubscribe(777);
        $this->assertEquals('{"seq_id":777}', json_encode($command));
    }
}
