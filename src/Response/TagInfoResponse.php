<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TagInfoResponse.
 *
 * @method int getAllowFollowing()
 * @method bool getAllowMutingStory()
 * @method mixed getDebugInfo()
 * @method int getFollowStatus()
 * @method int getFollowing()
 * @method string getId()
 * @method int getMediaCount()
 * @method mixed getMessage()
 * @method string getName()
 * @method string getProfilePicUrl()
 * @method mixed getRelatedTags()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isAllowFollowing()
 * @method bool isAllowMutingStory()
 * @method bool isDebugInfo()
 * @method bool isFollowStatus()
 * @method bool isFollowing()
 * @method bool isId()
 * @method bool isMediaCount()
 * @method bool isMessage()
 * @method bool isName()
 * @method bool isProfilePicUrl()
 * @method bool isRelatedTags()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setAllowFollowing(int $value)
 * @method $this setAllowMutingStory(bool $value)
 * @method $this setDebugInfo(mixed $value)
 * @method $this setFollowStatus(int $value)
 * @method $this setFollowing(int $value)
 * @method $this setId(string $value)
 * @method $this setMediaCount(int $value)
 * @method $this setMessage(mixed $value)
 * @method $this setName(string $value)
 * @method $this setProfilePicUrl(string $value)
 * @method $this setRelatedTags(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAllowFollowing()
 * @method $this unsetAllowMutingStory()
 * @method $this unsetDebugInfo()
 * @method $this unsetFollowStatus()
 * @method $this unsetFollowing()
 * @method $this unsetId()
 * @method $this unsetMediaCount()
 * @method $this unsetMessage()
 * @method $this unsetName()
 * @method $this unsetProfilePicUrl()
 * @method $this unsetRelatedTags()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class TagInfoResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        Model\Hashtag::class, // Import property map.
    ];
}
