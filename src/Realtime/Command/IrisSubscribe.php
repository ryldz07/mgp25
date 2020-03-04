<?php

namespace InstagramAPI\Realtime\Command;

use InstagramAPI\Realtime\CommandInterface;
use InstagramAPI\Realtime\Mqtt;

class IrisSubscribe implements CommandInterface
{
    const INVALID_SEQUENCE_ID = -1;

    /** @var int */
    private $_sequenceId;

    /**
     * Constructor.
     *
     * @param int $sequenceId
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $sequenceId)
    {
        if ($sequenceId === self::INVALID_SEQUENCE_ID) {
            throw new \InvalidArgumentException('Invalid Iris sequence identifier.');
        }
        $this->_sequenceId = $sequenceId;
    }

    /** {@inheritdoc} */
    public function getTopic()
    {
        return Mqtt\Topics::IRIS_SUB;
    }

    /** {@inheritdoc} */
    public function getQosLevel()
    {
        return Mqtt\QosLevel::ACKNOWLEDGED_DELIVERY;
    }

    /** {@inheritdoc} */
    public function jsonSerialize()
    {
        return [
            'seq_id' => $this->_sequenceId,
        ];
    }
}
