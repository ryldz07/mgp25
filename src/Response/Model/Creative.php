<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Creative.
 *
 * @method Text getContent()
 * @method mixed getDismissAction()
 * @method Text getFooter()
 * @method Image getImage()
 * @method Action getPrimaryAction()
 * @method Action getSecondaryAction()
 * @method Text getSocialContext()
 * @method Text getTitle()
 * @method bool isContent()
 * @method bool isDismissAction()
 * @method bool isFooter()
 * @method bool isImage()
 * @method bool isPrimaryAction()
 * @method bool isSecondaryAction()
 * @method bool isSocialContext()
 * @method bool isTitle()
 * @method $this setContent(Text $value)
 * @method $this setDismissAction(mixed $value)
 * @method $this setFooter(Text $value)
 * @method $this setImage(Image $value)
 * @method $this setPrimaryAction(Action $value)
 * @method $this setSecondaryAction(Action $value)
 * @method $this setSocialContext(Text $value)
 * @method $this setTitle(Text $value)
 * @method $this unsetContent()
 * @method $this unsetDismissAction()
 * @method $this unsetFooter()
 * @method $this unsetImage()
 * @method $this unsetPrimaryAction()
 * @method $this unsetSecondaryAction()
 * @method $this unsetSocialContext()
 * @method $this unsetTitle()
 */
class Creative extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'title'            => 'Text',
        'content'          => 'Text',
        'footer'           => 'Text',
        'social_context'   => 'Text',
        'content'          => 'Text',
        'primary_action'   => 'Action',
        'secondary_action' => 'Action',
        'dismiss_action'   => '',
        'image'            => 'Image',
    ];
}
