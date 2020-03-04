<?php

namespace InstagramAPI\Realtime\Command\Direct;

use InstagramAPI\Realtime\Command\DirectCommand;

abstract class SendItem extends DirectCommand
{
    const ACTION = 'send_item';

    /**
     * Constructor.
     *
     * @param string $threadId
     * @param string $itemType
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $threadId,
        $itemType,
        array $options = [])
    {
        parent::__construct(self::ACTION, $threadId, $options);

        // Handle action.
        if (!in_array($itemType, $this->_getSupportedItemTypes(), true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a supported item type.', $itemType));
        }
        $this->_data['item_type'] = $itemType;
    }

    /** {@inheritdoc} */
    protected function _isClientContextRequired()
    {
        return true;
    }

    /**
     * Get the list of supported item types.
     *
     * @return array
     */
    protected function _getSupportedItemTypes()
    {
        return [
            SendText::TYPE,
            SendLike::TYPE,
            SendReaction::TYPE,
            SendPost::TYPE,
            SendStory::TYPE,
            SendProfile::TYPE,
            SendLocation::TYPE,
            SendHashtag::TYPE,
        ];
    }
}
