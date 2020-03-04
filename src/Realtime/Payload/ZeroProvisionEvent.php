<?php

namespace InstagramAPI\Realtime\Payload;

use InstagramAPI\AutoPropertyMapper;

/**
 * ZeroProvisionEvent.
 *
 * @method string getDeviceId()
 * @method string getProductName()
 * @method string getZeroProvisionedTime()
 * @method bool isDeviceId()
 * @method bool isProductName()
 * @method bool isZeroProvisionedTime()
 * @method $this setDeviceId(string $value)
 * @method $this setProductName(string $value)
 * @method $this setZeroProvisionedTime(string $value)
 * @method $this unsetDeviceId()
 * @method $this unsetProductName()
 * @method $this unsetZeroProvisionedTime()
 */
class ZeroProvisionEvent extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'device_id'             => 'string',
        'product_name'          => 'string',
        'zero_provisioned_time' => 'string',
    ];
}
