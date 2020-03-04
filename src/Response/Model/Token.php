<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Token.
 *
 * @method int getCarrierId()
 * @method string getCarrierName()
 * @method string getDeadline()
 * @method mixed getEnabledWalletDefsKeys()
 * @method mixed getFeatures()
 * @method string getRequestTime()
 * @method RewriteRule[] getRewriteRules()
 * @method string getTokenHash()
 * @method int getTtl()
 * @method int getZeroCmsFetchIntervalSeconds()
 * @method bool isCarrierId()
 * @method bool isCarrierName()
 * @method bool isDeadline()
 * @method bool isEnabledWalletDefsKeys()
 * @method bool isFeatures()
 * @method bool isRequestTime()
 * @method bool isRewriteRules()
 * @method bool isTokenHash()
 * @method bool isTtl()
 * @method bool isZeroCmsFetchIntervalSeconds()
 * @method $this setCarrierId(int $value)
 * @method $this setCarrierName(string $value)
 * @method $this setDeadline(string $value)
 * @method $this setEnabledWalletDefsKeys(mixed $value)
 * @method $this setFeatures(mixed $value)
 * @method $this setRequestTime(string $value)
 * @method $this setRewriteRules(RewriteRule[] $value)
 * @method $this setTokenHash(string $value)
 * @method $this setTtl(int $value)
 * @method $this setZeroCmsFetchIntervalSeconds(int $value)
 * @method $this unsetCarrierId()
 * @method $this unsetCarrierName()
 * @method $this unsetDeadline()
 * @method $this unsetEnabledWalletDefsKeys()
 * @method $this unsetFeatures()
 * @method $this unsetRequestTime()
 * @method $this unsetRewriteRules()
 * @method $this unsetTokenHash()
 * @method $this unsetTtl()
 * @method $this unsetZeroCmsFetchIntervalSeconds()
 */
class Token extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'carrier_name'                    => 'string',
        'carrier_id'                      => 'int',
        'ttl'                             => 'int',
        'features'                        => '',
        'request_time'                    => 'string',
        'token_hash'                      => 'string',
        'rewrite_rules'                   => 'RewriteRule[]',
        'enabled_wallet_defs_keys'        => '',
        'deadline'                        => 'string',
        'zero_cms_fetch_interval_seconds' => 'int',
    ];

    const DEFAULT_TTL = 3600;

    /**
     * Get token expiration timestamp.
     *
     * @return int
     */
    public function expiresAt()
    {
        $ttl = (int) $this->getTtl();
        if ($ttl === 0) {
            $ttl = self::DEFAULT_TTL;
        }

        return time() + $ttl;
    }
}
