<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * CarouselMedia.
 *
 * @method string getAdAction()
 * @method int getAdLinkType()
 * @method AdMetadata[] getAdMetadata()
 * @method AndroidLinks[] getAndroidLinks()
 * @method string getCarouselParentId()
 * @method string getDominantColor()
 * @method string getDynamicItemId()
 * @method Usertag getFbUserTags()
 * @method bool getForceOverlay()
 * @method bool getHasAudio()
 * @method Headline getHeadline()
 * @method bool getHideNuxText()
 * @method string getId()
 * @method Image_Versions2 getImageVersions2()
 * @method int getIsDashEligible()
 * @method string getLink()
 * @method string getLinkHintText()
 * @method string getLinkText()
 * @method int getMediaType()
 * @method int getNumberOfQualities()
 * @method int getOriginalHeight()
 * @method int getOriginalWidth()
 * @method string getOverlaySubtitle()
 * @method string getOverlayText()
 * @method string getOverlayTitle()
 * @method string getPk()
 * @method string getPreview()
 * @method Usertag getUsertags()
 * @method string getVideoDashManifest()
 * @method float getVideoDuration()
 * @method string getVideoSubtitlesUri()
 * @method VideoVersions[] getVideoVersions()
 * @method bool isAdAction()
 * @method bool isAdLinkType()
 * @method bool isAdMetadata()
 * @method bool isAndroidLinks()
 * @method bool isCarouselParentId()
 * @method bool isDominantColor()
 * @method bool isDynamicItemId()
 * @method bool isFbUserTags()
 * @method bool isForceOverlay()
 * @method bool isHasAudio()
 * @method bool isHeadline()
 * @method bool isHideNuxText()
 * @method bool isId()
 * @method bool isImageVersions2()
 * @method bool isIsDashEligible()
 * @method bool isLink()
 * @method bool isLinkHintText()
 * @method bool isLinkText()
 * @method bool isMediaType()
 * @method bool isNumberOfQualities()
 * @method bool isOriginalHeight()
 * @method bool isOriginalWidth()
 * @method bool isOverlaySubtitle()
 * @method bool isOverlayText()
 * @method bool isOverlayTitle()
 * @method bool isPk()
 * @method bool isPreview()
 * @method bool isUsertags()
 * @method bool isVideoDashManifest()
 * @method bool isVideoDuration()
 * @method bool isVideoSubtitlesUri()
 * @method bool isVideoVersions()
 * @method $this setAdAction(string $value)
 * @method $this setAdLinkType(int $value)
 * @method $this setAdMetadata(AdMetadata[] $value)
 * @method $this setAndroidLinks(AndroidLinks[] $value)
 * @method $this setCarouselParentId(string $value)
 * @method $this setDominantColor(string $value)
 * @method $this setDynamicItemId(string $value)
 * @method $this setFbUserTags(Usertag $value)
 * @method $this setForceOverlay(bool $value)
 * @method $this setHasAudio(bool $value)
 * @method $this setHeadline(Headline $value)
 * @method $this setHideNuxText(bool $value)
 * @method $this setId(string $value)
 * @method $this setImageVersions2(Image_Versions2 $value)
 * @method $this setIsDashEligible(int $value)
 * @method $this setLink(string $value)
 * @method $this setLinkHintText(string $value)
 * @method $this setLinkText(string $value)
 * @method $this setMediaType(int $value)
 * @method $this setNumberOfQualities(int $value)
 * @method $this setOriginalHeight(int $value)
 * @method $this setOriginalWidth(int $value)
 * @method $this setOverlaySubtitle(string $value)
 * @method $this setOverlayText(string $value)
 * @method $this setOverlayTitle(string $value)
 * @method $this setPk(string $value)
 * @method $this setPreview(string $value)
 * @method $this setUsertags(Usertag $value)
 * @method $this setVideoDashManifest(string $value)
 * @method $this setVideoDuration(float $value)
 * @method $this setVideoSubtitlesUri(string $value)
 * @method $this setVideoVersions(VideoVersions[] $value)
 * @method $this unsetAdAction()
 * @method $this unsetAdLinkType()
 * @method $this unsetAdMetadata()
 * @method $this unsetAndroidLinks()
 * @method $this unsetCarouselParentId()
 * @method $this unsetDominantColor()
 * @method $this unsetDynamicItemId()
 * @method $this unsetFbUserTags()
 * @method $this unsetForceOverlay()
 * @method $this unsetHasAudio()
 * @method $this unsetHeadline()
 * @method $this unsetHideNuxText()
 * @method $this unsetId()
 * @method $this unsetImageVersions2()
 * @method $this unsetIsDashEligible()
 * @method $this unsetLink()
 * @method $this unsetLinkHintText()
 * @method $this unsetLinkText()
 * @method $this unsetMediaType()
 * @method $this unsetNumberOfQualities()
 * @method $this unsetOriginalHeight()
 * @method $this unsetOriginalWidth()
 * @method $this unsetOverlaySubtitle()
 * @method $this unsetOverlayText()
 * @method $this unsetOverlayTitle()
 * @method $this unsetPk()
 * @method $this unsetPreview()
 * @method $this unsetUsertags()
 * @method $this unsetVideoDashManifest()
 * @method $this unsetVideoDuration()
 * @method $this unsetVideoSubtitlesUri()
 * @method $this unsetVideoVersions()
 */
class CarouselMedia extends AutoPropertyMapper
{
    const PHOTO = 1;
    const VIDEO = 2;

    const JSON_PROPERTY_MAP = [
        'pk'                  => 'string',
        'id'                  => 'string',
        'carousel_parent_id'  => 'string',
        'fb_user_tags'        => 'Usertag',
        'number_of_qualities' => 'int',
        'is_dash_eligible'    => 'int',
        'video_dash_manifest' => 'string',
        'image_versions2'     => 'Image_Versions2',
        'video_versions'      => 'VideoVersions[]',
        'has_audio'           => 'bool',
        'video_duration'      => 'float',
        'video_subtitles_uri' => 'string',
        'original_height'     => 'int',
        'original_width'      => 'int',
        /*
         * A number describing what type of media this is. Should be compared
         * against the `CarouselMedia::PHOTO` and `CarouselMedia::VIDEO`
         * constants!
         */
        'media_type'          => 'int',
        'dynamic_item_id'     => 'string',
        'usertags'            => 'Usertag',
        'preview'             => 'string',
        'headline'            => 'Headline',
        'link'                => 'string',
        'link_text'           => 'string',
        'link_hint_text'      => 'string',
        'android_links'       => 'AndroidLinks[]',
        'ad_metadata'         => 'AdMetadata[]',
        'ad_action'           => 'string',
        'ad_link_type'        => 'int',
        'force_overlay'       => 'bool',
        'hide_nux_text'       => 'bool',
        'overlay_text'        => 'string',
        'overlay_title'       => 'string',
        'overlay_subtitle'    => 'string',
        /*
         * HTML color string such as "#812A2A".
         */
        'dominant_color'      => 'string',
    ];
}
