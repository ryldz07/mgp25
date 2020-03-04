<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * MeGraphData.
 *
 * @method string getId()
 * @method CatalogData getTaggableCatalogs()
 * @method bool isId()
 * @method bool isTaggableCatalogs()
 * @method $this setId(string $value)
 * @method $this setTaggableCatalogs(CatalogData $value)
 * @method $this unsetId()
 * @method $this unsetTaggableCatalogs()
 */
class MeGraphData extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'taggable_catalogs' => 'CatalogData',
        'id'                => 'string',
    ];
}
