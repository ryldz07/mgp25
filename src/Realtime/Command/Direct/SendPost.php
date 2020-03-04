<?php

namespace InstagramAPI\Realtime\Command\Direct;

final class SendPost extends ShareItem
{
    const TYPE = 'media_share';

    const MEDIA_REGEXP = '#^\d+_\d+$#D';

    /**
     * Constructor.
     *
     * @param string $threadId
     * @param string $mediaId
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $threadId,
        $mediaId,
        array $options = [])
    {
        parent::__construct($threadId, self::TYPE, $options);

        if (!preg_match(self::MEDIA_REGEXP, $mediaId)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid media ID.', $mediaId));
        }
        $this->_data['media_id'] = (string) $mediaId;
    }
}
