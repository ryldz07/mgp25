<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Hashtag.
 *
 * @method int getAllowFollowing()
 * @method bool getAllowMutingStory()
 * @method mixed getDebugInfo()
 * @method int getFollowStatus()
 * @method int getFollowing()
 * @method string getId()
 * @method int getMediaCount()
 * @method string getName()
 * @method string getProfilePicUrl()
 * @method mixed getRelatedTags()
 * @method bool isAllowFollowing()
 * @method bool isAllowMutingStory()
 * @method bool isDebugInfo()
 * @method bool isFollowStatus()
 * @method bool isFollowing()
 * @method bool isId()
 * @method bool isMediaCount()
 * @method bool isName()
 * @method bool isProfilePicUrl()
 * @method bool isRelatedTags()
 * @method $this setAllowFollowing(int $value)
 * @method $this setAllowMutingStory(bool $value)
 * @method $this setDebugInfo(mixed $value)
 * @method $this setFollowStatus(int $value)
 * @method $this setFollowing(int $value)
 * @method $this setId(string $value)
 * @method $this setMediaCount(int $value)
 * @method $this setName(string $value)
 * @method $this setProfilePicUrl(string $value)
 * @method $this setRelatedTags(mixed $value)
 * @method $this unsetAllowFollowing()
 * @method $this unsetAllowMutingStory()
 * @method $this unsetDebugInfo()
 * @method $this unsetFollowStatus()
 * @method $this unsetFollowing()
 * @method $this unsetId()
 * @method $this unsetMediaCount()
 * @method $this unsetName()
 * @method $this unsetProfilePicUrl()
 * @method $this unsetRelatedTags()
 */
class Hashtag extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'id'                 => 'string',
        'name'               => 'string',
        'media_count'        => 'int',
        'profile_pic_url'    => 'string',
        'follow_status'      => 'int',
        'following'          => 'int',
        'allow_following'    => 'int',
        'allow_muting_story' => 'bool',
        'related_tags'       => '',
        'debug_info'         => '',
    ];
}
