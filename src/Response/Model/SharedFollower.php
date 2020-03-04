<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * SharedFollower.
 *
 * @method string getFullName()
 * @method bool getHasAnonymousProfilePicture()
 * @method bool getIsPrivate()
 * @method bool getIsVerified()
 * @method string getOverlapScore()
 * @method string getPk()
 * @method string getProfilePicId()
 * @method string getProfilePicUrl()
 * @method string getReelAutoArchive()
 * @method string getUsername()
 * @method bool isFullName()
 * @method bool isHasAnonymousProfilePicture()
 * @method bool isIsPrivate()
 * @method bool isIsVerified()
 * @method bool isOverlapScore()
 * @method bool isPk()
 * @method bool isProfilePicId()
 * @method bool isProfilePicUrl()
 * @method bool isReelAutoArchive()
 * @method bool isUsername()
 * @method $this setFullName(string $value)
 * @method $this setHasAnonymousProfilePicture(bool $value)
 * @method $this setIsPrivate(bool $value)
 * @method $this setIsVerified(bool $value)
 * @method $this setOverlapScore(string $value)
 * @method $this setPk(string $value)
 * @method $this setProfilePicId(string $value)
 * @method $this setProfilePicUrl(string $value)
 * @method $this setReelAutoArchive(string $value)
 * @method $this setUsername(string $value)
 * @method $this unsetFullName()
 * @method $this unsetHasAnonymousProfilePicture()
 * @method $this unsetIsPrivate()
 * @method $this unsetIsVerified()
 * @method $this unsetOverlapScore()
 * @method $this unsetPk()
 * @method $this unsetProfilePicId()
 * @method $this unsetProfilePicUrl()
 * @method $this unsetReelAutoArchive()
 * @method $this unsetUsername()
 */
class SharedFollower extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'pk'                            => 'string',
        'username'                      => 'string',
        'full_name'                     => 'string',
        'is_private'                    => 'bool',
        'profile_pic_url'               => 'string',
        'profile_pic_id'                => 'string',
        'is_verified'                   => 'bool',
        'has_anonymous_profile_picture' => 'bool',
        'reel_auto_archive'             => 'string',
        'overlap_score'                 => 'string',
    ];
}
