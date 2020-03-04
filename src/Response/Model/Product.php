<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Product.
 *
 * @method string getCheckoutStyle()
 * @method string getCurrentPrice()
 * @method string getDescription()
 * @method string getExternalUrl()
 * @method string getFullPrice()
 * @method bool getHasViewerSaved()
 * @method ProductImage getMainImage()
 * @method string getName()
 * @method string getPrice()
 * @method string getProductId()
 * @method ProductImage[] getProductImages()
 * @method string getReviewStatus()
 * @method ProductImage getThumbnailImage()
 * @method bool isCheckoutStyle()
 * @method bool isCurrentPrice()
 * @method bool isDescription()
 * @method bool isExternalUrl()
 * @method bool isFullPrice()
 * @method bool isHasViewerSaved()
 * @method bool isMainImage()
 * @method bool isName()
 * @method bool isPrice()
 * @method bool isProductId()
 * @method bool isProductImages()
 * @method bool isReviewStatus()
 * @method bool isThumbnailImage()
 * @method $this setCheckoutStyle(string $value)
 * @method $this setCurrentPrice(string $value)
 * @method $this setDescription(string $value)
 * @method $this setExternalUrl(string $value)
 * @method $this setFullPrice(string $value)
 * @method $this setHasViewerSaved(bool $value)
 * @method $this setMainImage(ProductImage $value)
 * @method $this setName(string $value)
 * @method $this setPrice(string $value)
 * @method $this setProductId(string $value)
 * @method $this setProductImages(ProductImage[] $value)
 * @method $this setReviewStatus(string $value)
 * @method $this setThumbnailImage(ProductImage $value)
 * @method $this unsetCheckoutStyle()
 * @method $this unsetCurrentPrice()
 * @method $this unsetDescription()
 * @method $this unsetExternalUrl()
 * @method $this unsetFullPrice()
 * @method $this unsetHasViewerSaved()
 * @method $this unsetMainImage()
 * @method $this unsetName()
 * @method $this unsetPrice()
 * @method $this unsetProductId()
 * @method $this unsetProductImages()
 * @method $this unsetReviewStatus()
 * @method $this unsetThumbnailImage()
 */
class Product extends AutoPropertyMapper
{
    const APPROVED = 'approved';
    const PENDING = 'pending';
    const REJECTED = 'rejected';

    const JSON_PROPERTY_MAP = [
        'name'             => 'string',
        'price'            => 'string',
        'current_price'    => 'string',
        'full_price'       => 'string',
        'product_id'       => 'string',
        'has_viewer_saved' => 'bool',
        'description'      => 'string',
        'main_image'       => 'ProductImage',
        'thumbnail_image'  => 'ProductImage',
        'product_images'   => 'ProductImage[]',
        'external_url'     => 'string',
        'checkout_style'   => 'string',
        'review_status'    => 'string',
    ];
}
