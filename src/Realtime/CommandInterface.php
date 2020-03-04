<?php

namespace InstagramAPI\Realtime;

interface CommandInterface extends \JsonSerializable
{
    /**
     * Get the target topic.
     *
     * @return string
     */
    public function getTopic();

    /**
     * Get the MQTT QoS level.
     *
     * @return int
     */
    public function getQosLevel();
}
