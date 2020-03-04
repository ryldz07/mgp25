<?php

namespace InstagramAPI\React;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;

interface PersistentInterface
{
    /** @var int Minimum reconnection interval (in sec) */
    const MIN_RECONNECT_INTERVAL = 1;
    /** @var int Maximum reconnection interval (in sec) */
    const MAX_RECONNECT_INTERVAL = 300; // 5 minutes

    /**
     * Returns a minimum allowed reconnection interval.
     *
     * @return int
     */
    public function getMinReconnectInterval();

    /**
     * Returns a minimum allowed reconnection interval.
     *
     * @return int
     */
    public function getMaxReconnectInterval();

    /**
     * Returns whether persistence should be maintained.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Returns the logger instance.
     *
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * Returns the loop instance.
     *
     * @return LoopInterface
     */
    public function getLoop();
}
