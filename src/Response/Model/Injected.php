<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Injected.
 *
 * @method string getAboutAdParams()
 * @method string getAdId()
 * @method string getAdTitle()
 * @method string[] getCookies()
 * @method bool getDirectShare()
 * @method bool getDisplayViewabilityEligible()
 * @method string getFbPageUrl()
 * @method int getHideFlowType()
 * @method string getHideLabel()
 * @method HideReason[] getHideReasonsV2()
 * @method mixed getInvalidation()
 * @method bool getIsDemo()
 * @method bool getIsHoldout()
 * @method string getLabel()
 * @method string getLeadGenFormId()
 * @method bool getShowAdChoices()
 * @method bool getShowIcon()
 * @method string getTrackingToken()
 * @method mixed getViewTags()
 * @method bool isAboutAdParams()
 * @method bool isAdId()
 * @method bool isAdTitle()
 * @method bool isCookies()
 * @method bool isDirectShare()
 * @method bool isDisplayViewabilityEligible()
 * @method bool isFbPageUrl()
 * @method bool isHideFlowType()
 * @method bool isHideLabel()
 * @method bool isHideReasonsV2()
 * @method bool isInvalidation()
 * @method bool isIsDemo()
 * @method bool isIsHoldout()
 * @method bool isLabel()
 * @method bool isLeadGenFormId()
 * @method bool isShowAdChoices()
 * @method bool isShowIcon()
 * @method bool isTrackingToken()
 * @method bool isViewTags()
 * @method $this setAboutAdParams(string $value)
 * @method $this setAdId(string $value)
 * @method $this setAdTitle(string $value)
 * @method $this setCookies(string[] $value)
 * @method $this setDirectShare(bool $value)
 * @method $this setDisplayViewabilityEligible(bool $value)
 * @method $this setFbPageUrl(string $value)
 * @method $this setHideFlowType(int $value)
 * @method $this setHideLabel(string $value)
 * @method $this setHideReasonsV2(HideReason[] $value)
 * @method $this setInvalidation(mixed $value)
 * @method $this setIsDemo(bool $value)
 * @method $this setIsHoldout(bool $value)
 * @method $this setLabel(string $value)
 * @method $this setLeadGenFormId(string $value)
 * @method $this setShowAdChoices(bool $value)
 * @method $this setShowIcon(bool $value)
 * @method $this setTrackingToken(string $value)
 * @method $this setViewTags(mixed $value)
 * @method $this unsetAboutAdParams()
 * @method $this unsetAdId()
 * @method $this unsetAdTitle()
 * @method $this unsetCookies()
 * @method $this unsetDirectShare()
 * @method $this unsetDisplayViewabilityEligible()
 * @method $this unsetFbPageUrl()
 * @method $this unsetHideFlowType()
 * @method $this unsetHideLabel()
 * @method $this unsetHideReasonsV2()
 * @method $this unsetInvalidation()
 * @method $this unsetIsDemo()
 * @method $this unsetIsHoldout()
 * @method $this unsetLabel()
 * @method $this unsetLeadGenFormId()
 * @method $this unsetShowAdChoices()
 * @method $this unsetShowIcon()
 * @method $this unsetTrackingToken()
 * @method $this unsetViewTags()
 */
class Injected extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'label'                        => 'string',
        'show_icon'                    => 'bool',
        'hide_label'                   => 'string',
        'invalidation'                 => '', // Only encountered as NULL.
        'is_demo'                      => 'bool',
        'view_tags'                    => '', // Only seen as [].
        'is_holdout'                   => 'bool',
        'tracking_token'               => 'string',
        'show_ad_choices'              => 'bool',
        'ad_title'                     => 'string',
        'about_ad_params'              => 'string',
        'direct_share'                 => 'bool',
        'ad_id'                        => 'string',
        'display_viewability_eligible' => 'bool',
        'fb_page_url'                  => 'string',
        'hide_reasons_v2'              => 'HideReason[]',
        'hide_flow_type'               => 'int',
        'cookies'                      => 'string[]',
        'lead_gen_form_id'             => 'string',
    ];
}
