<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * ContextualFilters.
 *
 * @method string getClauseType()
 * @method mixed getClauses()
 * @method mixed getFilters()
 * @method bool isClauseType()
 * @method bool isClauses()
 * @method bool isFilters()
 * @method $this setClauseType(string $value)
 * @method $this setClauses(mixed $value)
 * @method $this setFilters(mixed $value)
 * @method $this unsetClauseType()
 * @method $this unsetClauses()
 * @method $this unsetFilters()
 */
class ContextualFilters extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'clause_type' => 'string',
        'filters'     => '',
        'clauses'     => '',
    ];
}
