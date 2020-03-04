<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * CatalogEdge.
 *
 * @method CatalogNode getNode()
 * @method bool isNode()
 * @method $this setNode(CatalogNode $value)
 * @method $this unsetNode()
 */
class CatalogEdge extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'node'          => 'CatalogNode',
    ];
}
