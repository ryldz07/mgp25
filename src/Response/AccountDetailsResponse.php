<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * AccountDetailsResponse.
 *
 * @method Model\AdsInfo getAdsInfo()
 * @method string getDateJoined()
 * @method Model\FormerUsernameInfo getFormerUsernameInfo()
 * @method mixed getMessage()
 * @method Model\PrimaryCountryInfo getPrimaryCountryInfo()
 * @method Model\SharedFollowerAccountsInfo getSharedFollowerAccountsInfo()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isAdsInfo()
 * @method bool isDateJoined()
 * @method bool isFormerUsernameInfo()
 * @method bool isMessage()
 * @method bool isPrimaryCountryInfo()
 * @method bool isSharedFollowerAccountsInfo()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setAdsInfo(Model\AdsInfo $value)
 * @method $this setDateJoined(string $value)
 * @method $this setFormerUsernameInfo(Model\FormerUsernameInfo $value)
 * @method $this setMessage(mixed $value)
 * @method $this setPrimaryCountryInfo(Model\PrimaryCountryInfo $value)
 * @method $this setSharedFollowerAccountsInfo(Model\SharedFollowerAccountsInfo $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAdsInfo()
 * @method $this unsetDateJoined()
 * @method $this unsetFormerUsernameInfo()
 * @method $this unsetMessage()
 * @method $this unsetPrimaryCountryInfo()
 * @method $this unsetSharedFollowerAccountsInfo()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class AccountDetailsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'date_joined'                   => 'string',
        'former_username_info'          => 'Model\FormerUsernameInfo',
        'primary_country_info'          => 'Model\PrimaryCountryInfo',
        'shared_follower_accounts_info' => 'Model\SharedFollowerAccountsInfo',
        'ads_info'                      => 'Model\AdsInfo',
    ];
}
