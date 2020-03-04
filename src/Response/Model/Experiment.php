<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Experiment.
 *
 * @method mixed getAdditionalParams()
 * @method bool getExpired()
 * @method string getGroup()
 * @method string getLoggingId()
 * @method string getName()
 * @method Param[] getParams()
 * @method bool isAdditionalParams()
 * @method bool isExpired()
 * @method bool isGroup()
 * @method bool isLoggingId()
 * @method bool isName()
 * @method bool isParams()
 * @method $this setAdditionalParams(mixed $value)
 * @method $this setExpired(bool $value)
 * @method $this setGroup(string $value)
 * @method $this setLoggingId(string $value)
 * @method $this setName(string $value)
 * @method $this setParams(Param[] $value)
 * @method $this unsetAdditionalParams()
 * @method $this unsetExpired()
 * @method $this unsetGroup()
 * @method $this unsetLoggingId()
 * @method $this unsetName()
 * @method $this unsetParams()
 */
class Experiment extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'name'              => 'string',
        'group'             => 'string',
        'additional_params' => '', // TODO: Only seen as [] empty array so far.
        'params'            => 'Param[]',
        'logging_id'        => 'string',
        'expired'           => 'bool',
    ];
}
