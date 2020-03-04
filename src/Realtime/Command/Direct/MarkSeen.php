<?php

namespace InstagramAPI\Realtime\Command\Direct;

use InstagramAPI\Realtime\Command\DirectCommand;

final class MarkSeen extends DirectCommand
{
    const ACTION = 'mark_seen';

    /**
     * Constructor.
     *
     * @param string $threadId
     * @param string $threadItemId
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $threadId,
        $threadItemId,
        array $options = [])
    {
        parent::__construct(self::ACTION, $threadId, $options);

        $this->_data['item_id'] = $this->_validateThreadItemId($threadItemId);
    }

    /** {@inheritdoc} */
    protected function _isClientContextRequired()
    {
        return false;
    }
}
