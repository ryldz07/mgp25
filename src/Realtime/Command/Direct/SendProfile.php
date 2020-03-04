<?php

namespace InstagramAPI\Realtime\Command\Direct;

final class SendProfile extends ShareItem
{
    const TYPE = 'profile';

    /**
     * Constructor.
     *
     * @param string $threadId
     * @param string $userId
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $threadId,
        $userId,
        array $options = [])
    {
        parent::__construct($threadId, self::TYPE, $options);

        if (!ctype_digit($userId) && (!is_int($userId) || $userId < 0)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid numerical UserPK ID.', $userId));
        }
        $this->_data['profile_user_id'] = (string) $userId;
        // Yeah, we need to send the user ID twice.
        $this->_data['item_id'] = (string) $userId;
    }
}
