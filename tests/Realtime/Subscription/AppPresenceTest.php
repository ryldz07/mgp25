<?php

namespace InstagramAPI\Tests\Subscription;

use InstagramAPI\Realtime\Subscription\GraphQl\AppPresenceSubscription;
use PHPUnit\Framework\TestCase;

class AppPresenceTest extends TestCase
{
    public function testCustomSubscriptionId()
    {
        $subscription = new AppPresenceSubscription('deadbeef-dead-beef-dead-beefdeadbeef');
        $this->assertEquals(
            '1/graphqlsubscriptions/17846944882223835/{"input_data":{"client_subscription_id":"deadbeef-dead-beef-dead-beefdeadbeef"}}',
            (string) $subscription
        );
    }
}
