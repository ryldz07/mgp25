<?php

namespace InstagramAPI\Realtime\Command\Direct;

final class SendText extends SendItem
{
    const TYPE = 'text';

    /**
     * Constructor.
     *
     * @param string $threadId
     * @param string $text
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $threadId,
        $text,
        array $options = [])
    {
        parent::__construct($threadId, self::TYPE, $options);

        if (!is_string($text)) {
            throw new \InvalidArgumentException('The text must be a string.');
        }

        if ($text === '') {
            throw new \InvalidArgumentException('The text can not be empty.');
        }
        $this->_data['text'] = $text;
    }
}
