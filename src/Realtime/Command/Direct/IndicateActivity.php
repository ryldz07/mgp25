<?php

namespace InstagramAPI\Realtime\Command\Direct;

use InstagramAPI\Realtime\Command\DirectCommand;

final class IndicateActivity extends DirectCommand
{
    const ACTION = 'indicate_activity';

    /**
     * Constructor.
     *
     * @param string $threadId
     * @param bool   $status
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $threadId,
        $status,
        array $options = [])
    {
        parent::__construct(self::ACTION, $threadId, $options);

        $this->_data['activity_status'] = $status ? '1' : '0';
    }

    /** {@inheritdoc} */
    protected function _isClientContextRequired()
    {
        return true;
    }
}
