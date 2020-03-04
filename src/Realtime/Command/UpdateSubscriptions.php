<?php

namespace InstagramAPI\Realtime\Command;

use InstagramAPI\Realtime\CommandInterface;
use InstagramAPI\Realtime\Mqtt\QosLevel;
use InstagramAPI\Utils;

class UpdateSubscriptions implements CommandInterface
{
    /** @var string */
    private $_topic;

    /** @var array */
    private $_subscribe;

    /** @var array */
    private $_unsubscribe;

    /**
     * Constructor.
     *
     * @param string $topic
     * @param array  $subscribe
     * @param array  $unsubscribe
     */
    public function __construct(
        $topic,
        array $subscribe,
        array $unsubscribe)
    {
        $this->_topic = $topic;
        $this->_subscribe = $subscribe;
        $this->_unsubscribe = $unsubscribe;
    }

    /** {@inheritdoc} */
    public function getTopic()
    {
        return $this->_topic;
    }

    /** {@inheritdoc} */
    public function getQosLevel()
    {
        return QosLevel::ACKNOWLEDGED_DELIVERY;
    }

    /**
     * Prepare the subscriptions list.
     *
     * @param array $subscriptions
     *
     * @return array
     */
    private function _prepareSubscriptions(
        array $subscriptions)
    {
        $result = [];
        foreach ($subscriptions as $subscription) {
            $result[] = (string) $subscription;
        }
        usort($result, function ($a, $b) {
            $hashA = Utils::hashCode($a);
            $hashB = Utils::hashCode($b);

            if ($hashA > $hashB) {
                return 1;
            }
            if ($hashA < $hashB) {
                return -1;
            }

            return 0;
        });

        return $result;
    }

    /** {@inheritdoc} */
    public function jsonSerialize()
    {
        $result = [];
        if (count($this->_subscribe)) {
            $result['sub'] = $this->_prepareSubscriptions($this->_subscribe);
        }
        if (count($this->_unsubscribe)) {
            $result['unsub'] = $this->_prepareSubscriptions($this->_unsubscribe);
        }

        return $result;
    }
}
