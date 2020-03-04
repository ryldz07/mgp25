<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Tag.
 *
 * @method mixed getAllowFollowing()
 * @method mixed getAllowMutingStory()
 * @method mixed getDebugInfo()
 * @method mixed getFollowButtonText()
 * @method mixed getFollowStatus()
 * @method mixed getFollowing()
 * @method string getId()
 * @method int getMediaCount()
 * @method string getName()
 * @method mixed getNonViolating()
 * @method mixed getProfilePicUrl()
 * @method mixed getRelatedTags()
 * @method mixed getSearchResultSubtitle()
 * @method mixed getShowFollowDropDown()
 * @method mixed getSocialContext()
 * @method mixed getSocialContextProfileLinks()
 * @method mixed getSubtitle()
 * @method string getType()
 * @method bool isAllowFollowing()
 * @method bool isAllowMutingStory()
 * @method bool isDebugInfo()
 * @method bool isFollowButtonText()
 * @method bool isFollowStatus()
 * @method bool isFollowing()
 * @method bool isId()
 * @method bool isMediaCount()
 * @method bool isName()
 * @method bool isNonViolating()
 * @method bool isProfilePicUrl()
 * @method bool isRelatedTags()
 * @method bool isSearchResultSubtitle()
 * @method bool isShowFollowDropDown()
 * @method bool isSocialContext()
 * @method bool isSocialContextProfileLinks()
 * @method bool isSubtitle()
 * @method bool isType()
 * @method $this setAllowFollowing(mixed $value)
 * @method $this setAllowMutingStory(mixed $value)
 * @method $this setDebugInfo(mixed $value)
 * @method $this setFollowButtonText(mixed $value)
 * @method $this setFollowStatus(mixed $value)
 * @method $this setFollowing(mixed $value)
 * @method $this setId(string $value)
 * @method $this setMediaCount(int $value)
 * @method $this setName(string $value)
 * @method $this setNonViolating(mixed $value)
 * @method $this setProfilePicUrl(mixed $value)
 * @method $this setRelatedTags(mixed $value)
 * @method $this setSearchResultSubtitle(mixed $value)
 * @method $this setShowFollowDropDown(mixed $value)
 * @method $this setSocialContext(mixed $value)
 * @method $this setSocialContextProfileLinks(mixed $value)
 * @method $this setSubtitle(mixed $value)
 * @method $this setType(string $value)
 * @method $this unsetAllowFollowing()
 * @method $this unsetAllowMutingStory()
 * @method $this unsetDebugInfo()
 * @method $this unsetFollowButtonText()
 * @method $this unsetFollowStatus()
 * @method $this unsetFollowing()
 * @method $this unsetId()
 * @method $this unsetMediaCount()
 * @method $this unsetName()
 * @method $this unsetNonViolating()
 * @method $this unsetProfilePicUrl()
 * @method $this unsetRelatedTags()
 * @method $this unsetSearchResultSubtitle()
 * @method $this unsetShowFollowDropDown()
 * @method $this unsetSocialContext()
 * @method $this unsetSocialContextProfileLinks()
 * @method $this unsetSubtitle()
 * @method $this unsetType()
 */
class Tag extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'id'                           => 'string',
        'name'                         => 'string',
        'media_count'                  => 'int',
        'type'                         => 'string',
        'follow_status'                => '',
        'following'                    => '',
        'allow_following'              => '',
        'allow_muting_story'           => '',
        'profile_pic_url'              => '',
        'non_violating'                => '',
        'related_tags'                 => '',
        'subtitle'                     => '',
        'social_context'               => '',
        'social_context_profile_links' => '',
        'show_follow_drop_down'        => '',
        'follow_button_text'           => '',
        'debug_info'                   => '',
        'search_result_subtitle'       => '',
    ];
}
