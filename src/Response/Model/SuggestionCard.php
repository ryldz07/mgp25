<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * SuggestionCard.
 *
 * @method mixed getUpsellCiCard()
 * @method mixed getUpsellFbcCard()
 * @method UserCard getUserCard()
 * @method bool isUpsellCiCard()
 * @method bool isUpsellFbcCard()
 * @method bool isUserCard()
 * @method $this setUpsellCiCard(mixed $value)
 * @method $this setUpsellFbcCard(mixed $value)
 * @method $this setUserCard(UserCard $value)
 * @method $this unsetUpsellCiCard()
 * @method $this unsetUpsellFbcCard()
 * @method $this unsetUserCard()
 */
class SuggestionCard extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'user_card'            => 'UserCard',
        'upsell_ci_card'       => '',
        'upsell_fbc_card'      => '',
    ];
}
