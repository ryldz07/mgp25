<?php

namespace InstagramAPI\Realtime\Command\Direct;

final class SendLocation extends ShareItem
{
    const TYPE = 'location';

    /**
     * Constructor.
     *
     * @param string $threadId
     * @param string $locationId
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $threadId,
        $locationId,
        array $options = [])
    {
        parent::__construct($threadId, self::TYPE, $options);

        if (!ctype_digit($locationId) && (!is_int($locationId) || $locationId < 0)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid location ID.', $locationId));
        }
        $this->_data['venue_id'] = (string) $locationId;
        // Yeah, we need to send the location ID twice.
        $this->_data['item_id'] = (string) $locationId;
    }
}
