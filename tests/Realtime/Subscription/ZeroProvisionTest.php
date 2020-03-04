<?php

namespace InstagramAPI\Tests\Subscription;

use InstagramAPI\Realtime\Subscription\GraphQl\ZeroProvisionSubscription;
use PHPUnit\Framework\TestCase;

class ZeroProvisionTest extends TestCase
{
    public function testDeviceId()
    {
        $subscription = new ZeroProvisionSubscription('deadbeef-dead-beef-dead-beefdeadbeef');
        $this->assertRegExp(
            '#^1/graphqlsubscriptions/17913953740109069/{"input_data":{"client_subscription_id":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}","device_id":"deadbeef-dead-beef-dead-beefdeadbeef"}}#',
            (string) $subscription
        );
    }
}
