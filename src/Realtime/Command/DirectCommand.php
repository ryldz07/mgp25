<?php

namespace InstagramAPI\Realtime\Command;

use InstagramAPI\Realtime\CommandInterface;
use InstagramAPI\Realtime\Mqtt;
use InstagramAPI\Signatures;

abstract class DirectCommand implements CommandInterface
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * Constructor.
     *
     * @param string $action
     * @param string $threadId
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $action,
        $threadId,
        array $options = [])
    {
        $this->_data = [];

        // Handle action.
        if (!in_array($action, $this->_getSupportedActions(), true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a supported action.', $action));
        }
        $this->_data['action'] = $action;

        $this->_data['thread_id'] = $this->_validateThreadId($threadId);

        // Handle client context.
        if ($this->_isClientContextRequired()) {
            if (!isset($options['client_context'])) {
                $this->_data['client_context'] = Signatures::generateUUID();
            } elseif (!Signatures::isValidUUID($options['client_context'])) {
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid UUID.', $options['client_context']));
            } else {
                $this->_data['client_context'] = $options['client_context'];
            }
        }
    }

    /** {@inheritdoc} */
    public function getTopic()
    {
        return Mqtt\Topics::SEND_MESSAGE;
    }

    /** {@inheritdoc} */
    public function getQosLevel()
    {
        return Mqtt\QosLevel::FIRE_AND_FORGET;
    }

    /** {@inheritdoc} */
    public function jsonSerialize()
    {
        return $this->_reorderFieldsByWeight($this->_data, $this->_getFieldsWeights());
    }

    /**
     * Get the client context.
     *
     * @return string|null
     */
    public function getClientContext()
    {
        return isset($this->_data['client_context']) ? $this->_data['client_context'] : null;
    }

    /**
     * Check whether client_context param is required.
     *
     * @return bool
     */
    abstract protected function _isClientContextRequired();

    /**
     * Get the list of supported actions.
     *
     * @return array
     */
    protected function _getSupportedActions()
    {
        return [
            Direct\SendItem::ACTION,
            Direct\MarkSeen::ACTION,
            Direct\IndicateActivity::ACTION,
        ];
    }

    /**
     * Validate given thread identifier.
     *
     * @param string $threadId
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function _validateThreadId(
        $threadId)
    {
        if (!ctype_digit($threadId) && (!is_int($threadId) || $threadId < 0)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid thread identifier.', $threadId));
        }

        return (string) $threadId;
    }

    /**
     * Validate given thread item identifier.
     *
     * @param string $threadItemId
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function _validateThreadItemId(
        $threadItemId)
    {
        if (!ctype_digit($threadItemId) && (!is_int($threadItemId) || $threadItemId < 0)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid thread item identifier.', $threadItemId));
        }

        return (string) $threadItemId;
    }

    /**
     * Reorders an array of fields by weights to simplify debugging.
     *
     * @param array $fields
     * @param array $weights
     *
     * @return array
     */
    protected function _reorderFieldsByWeight(
        array $fields,
        array $weights)
    {
        uksort($fields, function ($a, $b) use ($weights) {
            $a = isset($weights[$a]) ? $weights[$a] : PHP_INT_MAX;
            $b = isset($weights[$b]) ? $weights[$b] : PHP_INT_MAX;
            if ($a < $b) {
                return -1;
            }
            if ($a > $b) {
                return 1;
            }

            return 0;
        });

        return $fields;
    }

    /**
     * Get weights for fields.
     *
     * @return array
     */
    protected function _getFieldsWeights()
    {
        return [
            'thread_id'       => 10,
            'item_type'       => 15,
            'text'            => 20,
            'client_context'  => 25,
            'activity_status' => 30,
            'reaction_type'   => 35,
            'reaction_status' => 40,
            'item_id'         => 45,
            'node_type'       => 50,
            'action'          => 55,
            'profile_user_id' => 60,
            'hashtag'         => 65,
            'venue_id'        => 70,
            'media_id'        => 75,
        ];
    }
}
