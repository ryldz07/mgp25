<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * AdsInfo.
 *
 * @method string getAdsUrl()
 * @method bool getHasAds()
 * @method bool isAdsUrl()
 * @method bool isHasAds()
 * @method $this setAdsUrl(string $value)
 * @method $this setHasAds(bool $value)
 * @method $this unsetAdsUrl()
 * @method $this unsetHasAds()
 */
class AdsInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'has_ads' => 'bool',
        'ads_url' => 'string',
    ];
}
