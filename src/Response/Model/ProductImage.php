<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * ProductImage.
 *
 * @method Image_Versions2 getImageVersions2()
 * @method bool isImageVersions2()
 * @method $this setImageVersions2(Image_Versions2 $value)
 * @method $this unsetImageVersions2()
 */
class ProductImage extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'image_versions2' => 'Image_Versions2',
    ];
}
