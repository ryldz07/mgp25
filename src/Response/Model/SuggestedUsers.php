<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * SuggestedUsers.
 *
 * @method string getAutoDvance()
 * @method string getId()
 * @method string getLandingSiteTitle()
 * @method string getLandingSiteType()
 * @method string getNetegoType()
 * @method SuggestionCard[] getSuggestionCards()
 * @method Suggestion[] getSuggestions()
 * @method mixed getTitle()
 * @method string getTrackingToken()
 * @method int getType()
 * @method string getUpsellFbPos()
 * @method string getViewAllText()
 * @method bool isAutoDvance()
 * @method bool isId()
 * @method bool isLandingSiteTitle()
 * @method bool isLandingSiteType()
 * @method bool isNetegoType()
 * @method bool isSuggestionCards()
 * @method bool isSuggestions()
 * @method bool isTitle()
 * @method bool isTrackingToken()
 * @method bool isType()
 * @method bool isUpsellFbPos()
 * @method bool isViewAllText()
 * @method $this setAutoDvance(string $value)
 * @method $this setId(string $value)
 * @method $this setLandingSiteTitle(string $value)
 * @method $this setLandingSiteType(string $value)
 * @method $this setNetegoType(string $value)
 * @method $this setSuggestionCards(SuggestionCard[] $value)
 * @method $this setSuggestions(Suggestion[] $value)
 * @method $this setTitle(mixed $value)
 * @method $this setTrackingToken(string $value)
 * @method $this setType(int $value)
 * @method $this setUpsellFbPos(string $value)
 * @method $this setViewAllText(string $value)
 * @method $this unsetAutoDvance()
 * @method $this unsetId()
 * @method $this unsetLandingSiteTitle()
 * @method $this unsetLandingSiteType()
 * @method $this unsetNetegoType()
 * @method $this unsetSuggestionCards()
 * @method $this unsetSuggestions()
 * @method $this unsetTitle()
 * @method $this unsetTrackingToken()
 * @method $this unsetType()
 * @method $this unsetUpsellFbPos()
 * @method $this unsetViewAllText()
 */
class SuggestedUsers extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'id'                 => 'string',
        'view_all_text'      => 'string',
        'title'              => '',
        'auto_dvance'        => 'string',
        'type'               => 'int',
        'tracking_token'     => 'string',
        'landing_site_type'  => 'string',
        'landing_site_title' => 'string',
        'upsell_fb_pos'      => 'string',
        'suggestions'        => 'Suggestion[]',
        'suggestion_cards'   => 'SuggestionCard[]',
        'netego_type'        => 'string',
    ];
}
