<?php

namespace InstagramAPI\Media\Constraints;

/**
 * Instagram's direct messaging general media constraints.
 */
class DirectConstraints extends TimelineConstraints
{
    /**
     * Minimum allowed video duration.
     *
     * @var float
     */
    const MIN_DURATION = 0.1;

    /**
     * Maximum allowed video duration.
     *
     * @var float
     */
    const MAX_DURATION = 15.0;

    /** {@inheritdoc} */
    public function getTitle()
    {
        return 'direct';
    }

    /** {@inheritdoc} */
    public function getMinDuration()
    {
        return self::MIN_DURATION;
    }

    /** {@inheritdoc} */
    public function getMaxDuration()
    {
        return self::MAX_DURATION;
    }
}
