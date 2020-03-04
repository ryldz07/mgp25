<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * OnTagProductResponse.
 *
 * @method Model\User getMerchant()
 * @method mixed getMessage()
 * @method Model\Product[] getOtherProductItems()
 * @method Model\Product getProductItem()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isMerchant()
 * @method bool isMessage()
 * @method bool isOtherProductItems()
 * @method bool isProductItem()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMerchant(Model\User $value)
 * @method $this setMessage(mixed $value)
 * @method $this setOtherProductItems(Model\Product[] $value)
 * @method $this setProductItem(Model\Product $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMerchant()
 * @method $this unsetMessage()
 * @method $this unsetOtherProductItems()
 * @method $this unsetProductItem()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class OnTagProductResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'product_item'              => 'Model\Product',
        'merchant'                  => 'Model\User',
        'other_product_items'       => 'Model\Product[]',
    ];
}
