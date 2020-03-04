<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * PermanentItem.
 *
 * @method string getClientContext()
 * @method string getItemId()
 * @method string getItemType()
 * @method mixed getLike()
 * @method Link getLink()
 * @method LiveVideoShare getLiveVideoShare()
 * @method Location getLocation()
 * @method MediaData getMedia()
 * @method Item getMediaShare()
 * @method User getProfile()
 * @method ReelShare getReelShare()
 * @method string getText()
 * @method string getTimestamp()
 * @method string getUserId()
 * @method bool isClientContext()
 * @method bool isItemId()
 * @method bool isItemType()
 * @method bool isLike()
 * @method bool isLink()
 * @method bool isLiveVideoShare()
 * @method bool isLocation()
 * @method bool isMedia()
 * @method bool isMediaShare()
 * @method bool isProfile()
 * @method bool isReelShare()
 * @method bool isText()
 * @method bool isTimestamp()
 * @method bool isUserId()
 * @method $this setClientContext(string $value)
 * @method $this setItemId(string $value)
 * @method $this setItemType(string $value)
 * @method $this setLike(mixed $value)
 * @method $this setLink(Link $value)
 * @method $this setLiveVideoShare(LiveVideoShare $value)
 * @method $this setLocation(Location $value)
 * @method $this setMedia(MediaData $value)
 * @method $this setMediaShare(Item $value)
 * @method $this setProfile(User $value)
 * @method $this setReelShare(ReelShare $value)
 * @method $this setText(string $value)
 * @method $this setTimestamp(string $value)
 * @method $this setUserId(string $value)
 * @method $this unsetClientContext()
 * @method $this unsetItemId()
 * @method $this unsetItemType()
 * @method $this unsetLike()
 * @method $this unsetLink()
 * @method $this unsetLiveVideoShare()
 * @method $this unsetLocation()
 * @method $this unsetMedia()
 * @method $this unsetMediaShare()
 * @method $this unsetProfile()
 * @method $this unsetReelShare()
 * @method $this unsetText()
 * @method $this unsetTimestamp()
 * @method $this unsetUserId()
 */
class PermanentItem extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'item_id'          => 'string',
        'user_id'          => 'string',
        'timestamp'        => 'string',
        'item_type'        => 'string',
        'profile'          => 'User',
        'text'             => 'string',
        'location'         => 'Location',
        'like'             => '',
        'media'            => 'MediaData',
        'link'             => 'Link',
        'media_share'      => 'Item',
        'reel_share'       => 'ReelShare',
        'client_context'   => 'string',
        'live_video_share' => 'LiveVideoShare',
    ];
}
