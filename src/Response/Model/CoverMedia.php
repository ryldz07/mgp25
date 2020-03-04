<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * CoverMedia.
 *
 * @method int[] getCropRect()
 * @method ImageCandidate getCroppedImageVersion()
 * @method ImageCandidate getFullImageVersion()
 * @method string getId()
 * @method Image_Versions2 getImageVersions2()
 * @method string getMediaId()
 * @method int getMediaType()
 * @method int getOriginalHeight()
 * @method int getOriginalWidth()
 * @method bool isCropRect()
 * @method bool isCroppedImageVersion()
 * @method bool isFullImageVersion()
 * @method bool isId()
 * @method bool isImageVersions2()
 * @method bool isMediaId()
 * @method bool isMediaType()
 * @method bool isOriginalHeight()
 * @method bool isOriginalWidth()
 * @method $this setCropRect(int[] $value)
 * @method $this setCroppedImageVersion(ImageCandidate $value)
 * @method $this setFullImageVersion(ImageCandidate $value)
 * @method $this setId(string $value)
 * @method $this setImageVersions2(Image_Versions2 $value)
 * @method $this setMediaId(string $value)
 * @method $this setMediaType(int $value)
 * @method $this setOriginalHeight(int $value)
 * @method $this setOriginalWidth(int $value)
 * @method $this unsetCropRect()
 * @method $this unsetCroppedImageVersion()
 * @method $this unsetFullImageVersion()
 * @method $this unsetId()
 * @method $this unsetImageVersions2()
 * @method $this unsetMediaId()
 * @method $this unsetMediaType()
 * @method $this unsetOriginalHeight()
 * @method $this unsetOriginalWidth()
 */
class CoverMedia extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'id'              => 'string',
        'media_id'        => 'string',
        /*
         * A number describing what type of media this is.
         */
        'media_type'            => 'int',
        'image_versions2'       => 'Image_Versions2',
        'original_width'        => 'int',
        'original_height'       => 'int',
        'cropped_image_version' => 'ImageCandidate',
        'crop_rect'             => 'int[]',
        'full_image_version'    => 'ImageCandidate',
    ];
}
