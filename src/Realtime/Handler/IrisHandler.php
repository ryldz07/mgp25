<?php

namespace InstagramAPI\Realtime\Handler;

use InstagramAPI\Realtime\HandlerInterface;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\Payload\IrisSubscribeAck;

class IrisHandler extends AbstractHandler implements HandlerInterface
{
    const MODULE = 'iris';

    /** {@inheritdoc} */
    public function handleMessage(
        Message $message)
    {
        $iris = new IrisSubscribeAck($message->getData());
        if (!$iris->isSucceeded()) {
            throw new HandlerException(sprintf(
                'Failed to subscribe to Iris (%d): %s.',
                $iris->getErrorType(),
                $iris->getErrorMessage()
            ));
        }
        $this->_target->emit('iris-subscribed', [$iris]);
    }
}
