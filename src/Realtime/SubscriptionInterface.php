<?php

namespace InstagramAPI\Realtime;

interface SubscriptionInterface
{
    /**
     * Get the target MQTT topic.
     *
     * @return string
     */
    public function getTopic();

    /**
     * Get the unique subscription identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the string representation.
     *
     * @return string
     */
    public function __toString();
}
