<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * FeedAysf.
 *
 * @method mixed getActivator()
 * @method mixed getDisplayNewUnit()
 * @method mixed getFeedPosition()
 * @method mixed getFetchUserDetails()
 * @method mixed getIsDismissable()
 * @method mixed getLandingSiteTitle()
 * @method mixed getLandingSiteType()
 * @method mixed getShouldRefill()
 * @method Suggestion[] getSuggestions()
 * @method mixed getTitle()
 * @method string getUuid()
 * @method mixed getViewAllText()
 * @method bool isActivator()
 * @method bool isDisplayNewUnit()
 * @method bool isFeedPosition()
 * @method bool isFetchUserDetails()
 * @method bool isIsDismissable()
 * @method bool isLandingSiteTitle()
 * @method bool isLandingSiteType()
 * @method bool isShouldRefill()
 * @method bool isSuggestions()
 * @method bool isTitle()
 * @method bool isUuid()
 * @method bool isViewAllText()
 * @method $this setActivator(mixed $value)
 * @method $this setDisplayNewUnit(mixed $value)
 * @method $this setFeedPosition(mixed $value)
 * @method $this setFetchUserDetails(mixed $value)
 * @method $this setIsDismissable(mixed $value)
 * @method $this setLandingSiteTitle(mixed $value)
 * @method $this setLandingSiteType(mixed $value)
 * @method $this setShouldRefill(mixed $value)
 * @method $this setSuggestions(Suggestion[] $value)
 * @method $this setTitle(mixed $value)
 * @method $this setUuid(string $value)
 * @method $this setViewAllText(mixed $value)
 * @method $this unsetActivator()
 * @method $this unsetDisplayNewUnit()
 * @method $this unsetFeedPosition()
 * @method $this unsetFetchUserDetails()
 * @method $this unsetIsDismissable()
 * @method $this unsetLandingSiteTitle()
 * @method $this unsetLandingSiteType()
 * @method $this unsetShouldRefill()
 * @method $this unsetSuggestions()
 * @method $this unsetTitle()
 * @method $this unsetUuid()
 * @method $this unsetViewAllText()
 */
class FeedAysf extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'landing_site_type'  => '',
        'uuid'               => 'string',
        'view_all_text'      => '',
        'feed_position'      => '',
        'landing_site_title' => '',
        'is_dismissable'     => '',
        'suggestions'        => 'Suggestion[]',
        'should_refill'      => '',
        'display_new_unit'   => '',
        'fetch_user_details' => '',
        'title'              => '',
        'activator'          => '',
    ];
}
