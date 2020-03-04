<?php

namespace InstagramAPI\React;

use React\EventLoop\Timer\TimerInterface;
use React\Promise\PromiseInterface;

/**
 * @mixin PersistentInterface
 */
trait PersistentTrait
{
    /** @var int */
    protected $_reconnectInterval;

    /** @var TimerInterface */
    protected $_reconnectTimer;

    /**
     * Cancel a reconnect timer (if any).
     */
    protected function _cancelReconnectTimer()
    {
        if ($this->_reconnectTimer !== null) {
            if ($this->_reconnectTimer->isActive()) {
                $this->getLogger()->info('Existing reconnect timer has been canceled.');
                $this->_reconnectTimer->cancel();
            }
            $this->_reconnectTimer = null;
        }
    }

    /**
     * Set up a new reconnect timer with exponential backoff.
     *
     * @param callable $callback
     */
    protected function _setReconnectTimer(
        callable $callback)
    {
        $this->_cancelReconnectTimer();
        if (!$this->isActive()) {
            return;
        }
        $this->_reconnectInterval = min(
            $this->getMaxReconnectInterval(),
            max(
                $this->getMinReconnectInterval(),
                $this->_reconnectInterval * 2
            )
        );
        $this->getLogger()->info(sprintf('Setting up reconnect timer to %d seconds.', $this->_reconnectInterval));
        $this->_reconnectTimer = $this->getLoop()->addTimer($this->_reconnectInterval, function () use ($callback) {
            /** @var PromiseInterface $promise */
            $promise = $callback();
            $promise->then(
                function () {
                    // Reset reconnect interval on successful connection attempt.
                    $this->_reconnectInterval = 0;
                },
                function () use ($callback) {
                    $this->_setReconnectTimer($callback);
                }
            );
        });
    }

    /** {@inheritdoc} */
    public function getMinReconnectInterval()
    {
        return PersistentInterface::MIN_RECONNECT_INTERVAL;
    }

    /** {@inheritdoc} */
    public function getMaxReconnectInterval()
    {
        return PersistentInterface::MAX_RECONNECT_INTERVAL;
    }
}
