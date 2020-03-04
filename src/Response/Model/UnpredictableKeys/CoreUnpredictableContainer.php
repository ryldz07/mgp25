<?php

namespace InstagramAPI\Response\Model\UnpredictableKeys;

use InstagramAPI\AutoPropertyMapper;
use LazyJsonMapper\Exception\LazyJsonMapperException;

/**
 * This class defines a core "untyped" container of unpredictable data-keys.
 *
 * Unpredictable data is data with keys that cannot be known ahead of time.
 * Such as objects whose values are keyed by things like user IDs, for example:
 * `{"9323":{"name":"foo"}}`
 *
 * The `getData()` function retrieves all key-value pairs, converted to the
 * optional `$_type` (if one is set via a subclass). And `setData()` writes
 * the new data back into the core LazyJsonMapper container. Most people will
 * not need to use the setter. It's just provided as an extra feature.
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class CoreUnpredictableContainer extends AutoPropertyMapper
{
    // Let's disable direct access to this container via anything other than
    // the functions that WE define ourselves! That way, people cannot use
    // virtual properties/functions to manipulate the core data storage.
    const ALLOW_VIRTUAL_PROPERTIES = false;
    const ALLOW_VIRTUAL_FUNCTIONS = false;

    /**
     * Data cache to avoid constant processing every time the getter is used.
     *
     * @var array
     */
    protected $_cache;

    /**
     * What type to convert all sub-objects/values into.
     *
     * Defaults to no conversion. Override this value via a subclass!
     *
     * @var string
     */
    protected $_type;

    /**
     * Get the data array of this unpredictable container.
     *
     * @throws LazyJsonMapperException
     *
     * @return array
     */
    public function getData()
    {
        if ($this->_cache === null) {
            $this->_cache = $this->asArray(); // Throws.
        }

        if ($this->_type !== null) {
            foreach ($this->_cache as &$value) {
                if (is_array($value)) {
                    $value = new $this->_type($value); // Throws.
                }
            }
        }

        return $this->_cache;
    }

    /**
     * Set the data array of this unpredictable container.
     *
     * @param array $value The new data array.
     *
     * @throws LazyJsonMapperException
     *
     * @return $this
     */
    public function setData(
        array $value)
    {
        $this->_cache = $value;

        $newObjectData = [];
        foreach ($this->_cache as $k => $v) {
            $newObjectData[$k] = is_object($v) && $v instanceof LazyJsonMapper
                               ? $v->asArray() // Throws.
                               : $v; // Is already a valid value.
        }

        $this->assignObjectData($newObjectData); // Throws.

        return $this;
    }
}
