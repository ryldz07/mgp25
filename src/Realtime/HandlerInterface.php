<?php

namespace InstagramAPI\Realtime;

use InstagramAPI\Realtime\Handler\HandlerException;

interface HandlerInterface
{
    /**
     * Handle the message.
     *
     * @param Message $message
     *
     * @throws HandlerException
     */
    public function handleMessage(
        Message $message);
}
