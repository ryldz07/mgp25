<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * UserCard.
 *
 * @method string getAlgorithm()
 * @method mixed getCaption()
 * @method bool getFollowedBy()
 * @method mixed getIcon()
 * @method bool getIsNewSuggestion()
 * @method mixed getLargeUrls()
 * @method mixed getMediaIds()
 * @method mixed getMediaInfos()
 * @method string getSocialContext()
 * @method mixed getThumbnailUrls()
 * @method User getUser()
 * @method string getUuid()
 * @method float getValue()
 * @method bool isAlgorithm()
 * @method bool isCaption()
 * @method bool isFollowedBy()
 * @method bool isIcon()
 * @method bool isIsNewSuggestion()
 * @method bool isLargeUrls()
 * @method bool isMediaIds()
 * @method bool isMediaInfos()
 * @method bool isSocialContext()
 * @method bool isThumbnailUrls()
 * @method bool isUser()
 * @method bool isUuid()
 * @method bool isValue()
 * @method $this setAlgorithm(string $value)
 * @method $this setCaption(mixed $value)
 * @method $this setFollowedBy(bool $value)
 * @method $this setIcon(mixed $value)
 * @method $this setIsNewSuggestion(bool $value)
 * @method $this setLargeUrls(mixed $value)
 * @method $this setMediaIds(mixed $value)
 * @method $this setMediaInfos(mixed $value)
 * @method $this setSocialContext(string $value)
 * @method $this setThumbnailUrls(mixed $value)
 * @method $this setUser(User $value)
 * @method $this setUuid(string $value)
 * @method $this setValue(float $value)
 * @method $this unsetAlgorithm()
 * @method $this unsetCaption()
 * @method $this unsetFollowedBy()
 * @method $this unsetIcon()
 * @method $this unsetIsNewSuggestion()
 * @method $this unsetLargeUrls()
 * @method $this unsetMediaIds()
 * @method $this unsetMediaInfos()
 * @method $this unsetSocialContext()
 * @method $this unsetThumbnailUrls()
 * @method $this unsetUser()
 * @method $this unsetUuid()
 * @method $this unsetValue()
 */
class UserCard extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'user'              => 'User',
        'algorithm'         => 'string',
        'social_context'    => 'string',
        'caption'           => '',
        'icon'              => '',
        'media_ids'         => '',
        'thumbnail_urls'    => '',
        'large_urls'        => '',
        'media_infos'       => '',
        'value'             => 'float',
        'is_new_suggestion' => 'bool',
        'uuid'              => 'string',
        'followed_by'       => 'bool',
    ];
}
