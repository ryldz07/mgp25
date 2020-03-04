<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * AndroidLinks.
 *
 * @method string getAndroidClass()
 * @method string getCallToActionTitle()
 * @method string getCanvasDocId()
 * @method string getDeeplinkUri()
 * @method string getIgUserId()
 * @method string getLeadGenFormId()
 * @method int getLinkType()
 * @method string getPackage()
 * @method string getRedirectUri()
 * @method string getWebUri()
 * @method bool isAndroidClass()
 * @method bool isCallToActionTitle()
 * @method bool isCanvasDocId()
 * @method bool isDeeplinkUri()
 * @method bool isIgUserId()
 * @method bool isLeadGenFormId()
 * @method bool isLinkType()
 * @method bool isPackage()
 * @method bool isRedirectUri()
 * @method bool isWebUri()
 * @method $this setAndroidClass(string $value)
 * @method $this setCallToActionTitle(string $value)
 * @method $this setCanvasDocId(string $value)
 * @method $this setDeeplinkUri(string $value)
 * @method $this setIgUserId(string $value)
 * @method $this setLeadGenFormId(string $value)
 * @method $this setLinkType(int $value)
 * @method $this setPackage(string $value)
 * @method $this setRedirectUri(string $value)
 * @method $this setWebUri(string $value)
 * @method $this unsetAndroidClass()
 * @method $this unsetCallToActionTitle()
 * @method $this unsetCanvasDocId()
 * @method $this unsetDeeplinkUri()
 * @method $this unsetIgUserId()
 * @method $this unsetLeadGenFormId()
 * @method $this unsetLinkType()
 * @method $this unsetPackage()
 * @method $this unsetRedirectUri()
 * @method $this unsetWebUri()
 */
class AndroidLinks extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'linkType'          => 'int',
        'webUri'            => 'string',
        'androidClass'      => 'string',
        'package'           => 'string',
        'deeplinkUri'       => 'string',
        'callToActionTitle' => 'string',
        'redirectUri'       => 'string',
        'igUserId'          => 'string',
        'leadGenFormId'     => 'string',
        'canvasDocId'       => 'string',
    ];
}
