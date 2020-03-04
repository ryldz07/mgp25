<?php

namespace InstagramAPI\Realtime\Handler;

use InstagramAPI\Realtime\HandlerInterface;
use InstagramAPI\Realtime\Message;

class RegionHintHandler extends AbstractHandler implements HandlerInterface
{
    const MODULE = 'region_hint';

    /** {@inheritdoc} */
    public function handleMessage(
        Message $message)
    {
        $region = $message->getData();
        if ($region === null || $region === '') {
            throw new HandlerException('Invalid region hint.');
        }
        $this->_target->emit('region-hint', [$message->getData()]);
    }
}
