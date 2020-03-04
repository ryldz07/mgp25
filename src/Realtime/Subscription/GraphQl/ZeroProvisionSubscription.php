<?php

namespace InstagramAPI\Realtime\Subscription\GraphQl;

use InstagramAPI\Realtime\Subscription\GraphQlSubscription;
use InstagramAPI\Signatures;

class ZeroProvisionSubscription extends GraphQlSubscription
{
    const QUERY = '17913953740109069';
    const ID = 'zero_provision';

    /**
     * Constructor.
     *
     * @param string $deviceId
     */
    public function __construct(
        $deviceId)
    {
        parent::__construct(self::QUERY, [
            'client_subscription_id' => Signatures::generateUUID(),
            'device_id'              => $deviceId,
        ]);
    }

    /** {@inheritdoc} */
    public function getId()
    {
        return self::ID;
    }
}
