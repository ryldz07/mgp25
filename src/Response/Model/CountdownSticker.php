<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * CountdownSticker.
 *
 * @method mixed getAttribution()
 * @method string getCountdownId()
 * @method string getDigitCardColor()
 * @method string getDigitColor()
 * @method string getEndBackgroundColor()
 * @method string getEndTs()
 * @method bool getFollowingEnabled()
 * @method bool getIsOwner()
 * @method string getStartBackgroundColor()
 * @method string getText()
 * @method string getTextColor()
 * @method bool getViewerIsFollowing()
 * @method bool isAttribution()
 * @method bool isCountdownId()
 * @method bool isDigitCardColor()
 * @method bool isDigitColor()
 * @method bool isEndBackgroundColor()
 * @method bool isEndTs()
 * @method bool isFollowingEnabled()
 * @method bool isIsOwner()
 * @method bool isStartBackgroundColor()
 * @method bool isText()
 * @method bool isTextColor()
 * @method bool isViewerIsFollowing()
 * @method $this setAttribution(mixed $value)
 * @method $this setCountdownId(string $value)
 * @method $this setDigitCardColor(string $value)
 * @method $this setDigitColor(string $value)
 * @method $this setEndBackgroundColor(string $value)
 * @method $this setEndTs(string $value)
 * @method $this setFollowingEnabled(bool $value)
 * @method $this setIsOwner(bool $value)
 * @method $this setStartBackgroundColor(string $value)
 * @method $this setText(string $value)
 * @method $this setTextColor(string $value)
 * @method $this setViewerIsFollowing(bool $value)
 * @method $this unsetAttribution()
 * @method $this unsetCountdownId()
 * @method $this unsetDigitCardColor()
 * @method $this unsetDigitColor()
 * @method $this unsetEndBackgroundColor()
 * @method $this unsetEndTs()
 * @method $this unsetFollowingEnabled()
 * @method $this unsetIsOwner()
 * @method $this unsetStartBackgroundColor()
 * @method $this unsetText()
 * @method $this unsetTextColor()
 * @method $this unsetViewerIsFollowing()
 */
class CountdownSticker extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'countdown_id'           => 'string',
        'end_ts'                 => 'string',
        'text'                   => 'string',
        /*
         * HTML color string such as "#812A2A".
         */
        'text_color'             => 'string',
        /*
         * HTML color string such as "#812A2A".
         */
        'start_background_color' => 'string',
        /*
         * HTML color string such as "#812A2A".
         */
        'end_background_color'   => 'string',
        /*
         * HTML color string such as "#812A2A".
         */
        'digit_color'            => 'string',
        /*
         * HTML color string such as "#812A2A".
         */
        'digit_card_color'       => 'string',
        'following_enabled'      => 'bool',
        'is_owner'               => 'bool',
        'attribution'            => '',
        'viewer_is_following'    => 'bool',
    ];
}
