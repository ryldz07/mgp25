<?php

namespace InstagramAPI\Tests\Realtime\Command;

use InstagramAPI\Realtime\Command\UpdateSubscriptions;
use PHPUnit\Framework\TestCase;

class UpdateSubscriptionsTest extends TestCase
{
    public function testMultipleSubscriptionsOrder()
    {
        $command = new UpdateSubscriptions(
            '/pubsub',
            ['ig/u/v1/1111111111', 'ig/live_notification_subscribe/1111111111'],
            []
        );
        $this->assertEquals(
            '{"sub":["ig/live_notification_subscribe/1111111111","ig/u/v1/1111111111"]}',
            json_encode($command, JSON_UNESCAPED_SLASHES)
        );
    }
}
