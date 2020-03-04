<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * OnBoardCatalogResponse.
 *
 * @method string getCurrentCatalogId()
 * @method bool getIsBusinessTargetedForShopping()
 * @method mixed getMessage()
 * @method string getShoppingOnboardingState()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isCurrentCatalogId()
 * @method bool isIsBusinessTargetedForShopping()
 * @method bool isMessage()
 * @method bool isShoppingOnboardingState()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setCurrentCatalogId(string $value)
 * @method $this setIsBusinessTargetedForShopping(bool $value)
 * @method $this setMessage(mixed $value)
 * @method $this setShoppingOnboardingState(string $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetCurrentCatalogId()
 * @method $this unsetIsBusinessTargetedForShopping()
 * @method $this unsetMessage()
 * @method $this unsetShoppingOnboardingState()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class OnBoardCatalogResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'shopping_onboarding_state'         => 'string',
        'current_catalog_id'                => 'string',
        'is_business_targeted_for_shopping' => 'bool',
    ];
}
