<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * QPNode.
 *
 * @method ContextualFilters getContextualFilters()
 * @method Creative[] getCreatives()
 * @method string getId()
 * @method int getMaxImpressions()
 * @method string getPromotionId()
 * @method Template getTemplate()
 * @method string[] getTriggers()
 * @method bool isContextualFilters()
 * @method bool isCreatives()
 * @method bool isId()
 * @method bool isMaxImpressions()
 * @method bool isPromotionId()
 * @method bool isTemplate()
 * @method bool isTriggers()
 * @method $this setContextualFilters(ContextualFilters $value)
 * @method $this setCreatives(Creative[] $value)
 * @method $this setId(string $value)
 * @method $this setMaxImpressions(int $value)
 * @method $this setPromotionId(string $value)
 * @method $this setTemplate(Template $value)
 * @method $this setTriggers(string[] $value)
 * @method $this unsetContextualFilters()
 * @method $this unsetCreatives()
 * @method $this unsetId()
 * @method $this unsetMaxImpressions()
 * @method $this unsetPromotionId()
 * @method $this unsetTemplate()
 * @method $this unsetTriggers()
 */
class QPNode extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'id'                 => 'string',
        'promotion_id'       => 'string',
        'max_impressions'    => 'int',
        'triggers'           => 'string[]',
        'contextual_filters' => 'ContextualFilters',
        'template'           => 'Template',
        'creatives'          => 'Creative[]',
    ];
}
