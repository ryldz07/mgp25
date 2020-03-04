<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Args.
 *
 * @method string getActionUrl()
 * @method string[] getActions()
 * @method bool getClicked()
 * @method string getCommentId()
 * @method string[] getCommentIds()
 * @method string getCommentNotifType()
 * @method string getDestination()
 * @method string getIconUrl()
 * @method InlineFollow getInlineFollow()
 * @method string getLatestReelMedia()
 * @method Link[] getLinks()
 * @method Media[] getMedia()
 * @method string getMediaDestination()
 * @method string getProfileId()
 * @method string getProfileImage()
 * @method mixed getProfileImageDestination()
 * @method string getProfileName()
 * @method mixed getRequestCount()
 * @method string getRichText()
 * @method string getSecondProfileId()
 * @method mixed getSecondProfileImage()
 * @method string getText()
 * @method string getTimestamp()
 * @method string getTuuid()
 * @method bool isActionUrl()
 * @method bool isActions()
 * @method bool isClicked()
 * @method bool isCommentId()
 * @method bool isCommentIds()
 * @method bool isCommentNotifType()
 * @method bool isDestination()
 * @method bool isIconUrl()
 * @method bool isInlineFollow()
 * @method bool isLatestReelMedia()
 * @method bool isLinks()
 * @method bool isMedia()
 * @method bool isMediaDestination()
 * @method bool isProfileId()
 * @method bool isProfileImage()
 * @method bool isProfileImageDestination()
 * @method bool isProfileName()
 * @method bool isRequestCount()
 * @method bool isRichText()
 * @method bool isSecondProfileId()
 * @method bool isSecondProfileImage()
 * @method bool isText()
 * @method bool isTimestamp()
 * @method bool isTuuid()
 * @method $this setActionUrl(string $value)
 * @method $this setActions(string[] $value)
 * @method $this setClicked(bool $value)
 * @method $this setCommentId(string $value)
 * @method $this setCommentIds(string[] $value)
 * @method $this setCommentNotifType(string $value)
 * @method $this setDestination(string $value)
 * @method $this setIconUrl(string $value)
 * @method $this setInlineFollow(InlineFollow $value)
 * @method $this setLatestReelMedia(string $value)
 * @method $this setLinks(Link[] $value)
 * @method $this setMedia(Media[] $value)
 * @method $this setMediaDestination(string $value)
 * @method $this setProfileId(string $value)
 * @method $this setProfileImage(string $value)
 * @method $this setProfileImageDestination(mixed $value)
 * @method $this setProfileName(string $value)
 * @method $this setRequestCount(mixed $value)
 * @method $this setRichText(string $value)
 * @method $this setSecondProfileId(string $value)
 * @method $this setSecondProfileImage(mixed $value)
 * @method $this setText(string $value)
 * @method $this setTimestamp(string $value)
 * @method $this setTuuid(string $value)
 * @method $this unsetActionUrl()
 * @method $this unsetActions()
 * @method $this unsetClicked()
 * @method $this unsetCommentId()
 * @method $this unsetCommentIds()
 * @method $this unsetCommentNotifType()
 * @method $this unsetDestination()
 * @method $this unsetIconUrl()
 * @method $this unsetInlineFollow()
 * @method $this unsetLatestReelMedia()
 * @method $this unsetLinks()
 * @method $this unsetMedia()
 * @method $this unsetMediaDestination()
 * @method $this unsetProfileId()
 * @method $this unsetProfileImage()
 * @method $this unsetProfileImageDestination()
 * @method $this unsetProfileName()
 * @method $this unsetRequestCount()
 * @method $this unsetRichText()
 * @method $this unsetSecondProfileId()
 * @method $this unsetSecondProfileImage()
 * @method $this unsetText()
 * @method $this unsetTimestamp()
 * @method $this unsetTuuid()
 */
class Args extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'media_destination'         => 'string',
        'text'                      => 'string',
        'icon_url'                  => 'string',
        'links'                     => 'Link[]',
        'rich_text'                 => 'string',
        'profile_id'                => 'string',
        'profile_image'             => 'string',
        'media'                     => 'Media[]',
        'comment_notif_type'        => 'string',
        'timestamp'                 => 'string',
        'tuuid'                     => 'string',
        'clicked'                   => 'bool',
        'profile_name'              => 'string',
        'action_url'                => 'string',
        'destination'               => 'string',
        'actions'                   => 'string[]',
        'latest_reel_media'         => 'string',
        'comment_id'                => 'string',
        'request_count'             => '',
        'inline_follow'             => 'InlineFollow',
        'comment_ids'               => 'string[]',
        'second_profile_id'         => 'string',
        'second_profile_image'      => '',
        'profile_image_destination' => '',
    ];
}
