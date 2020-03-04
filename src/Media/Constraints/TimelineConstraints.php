<?php

namespace InstagramAPI\Media\Constraints;

use InstagramAPI\Media\ConstraintsInterface;

/**
 * Instagram's timeline general media constraints.
 */
class TimelineConstraints implements ConstraintsInterface
{
    /**
     * Lowest allowed general media aspect ratio (4:5, meaning portrait).
     *
     * These are decided by Instagram. Not by us!
     *
     * @var float
     *
     * @see https://help.instagram.com/1469029763400082
     */
    const MIN_RATIO = 0.8;

    /**
     * Highest allowed general media aspect ratio (1.91:1, meaning landscape).
     *
     * These are decided by Instagram. Not by us!
     *
     * @var float
     */
    const MAX_RATIO = 1.91;

    /**
     * The recommended aspect ratio for timeline media.
     *
     * This creates square media, which is Instagram's "standard" format.
     *
     * @var float
     */
    const RECOMMENDED_RATIO = 1.0;

    /**
     * The deviation for the recommended aspect ratio.
     *
     * We can always resize to 1:1, so we allow no deviation here.
     *
     * @var float
     */
    const RECOMMENDED_RATIO_DEVIATION = 0.0;

    /**
     * Minimum allowed video duration.
     *
     * @var float
     *
     * @see https://help.instagram.com/270963803047681
     */
    const MIN_DURATION = 3.0;

    /**
     * Maximum allowed video duration.
     *
     * @var float
     *
     * @see https://help.instagram.com/270963803047681
     */
    const MAX_DURATION = 60.0;

    /** {@inheritdoc} */
    public function getTitle()
    {
        return 'timeline';
    }

    /** {@inheritdoc} */
    public function getMinAspectRatio()
    {
        return self::MIN_RATIO;
    }

    /** {@inheritdoc} */
    public function getMaxAspectRatio()
    {
        return self::MAX_RATIO;
    }

    /** {@inheritdoc} */
    public function getRecommendedRatio()
    {
        return self::RECOMMENDED_RATIO;
    }

    /** {@inheritdoc} */
    public function getRecommendedRatioDeviation()
    {
        return self::RECOMMENDED_RATIO_DEVIATION;
    }

    /** {@inheritdoc} */
    public function useRecommendedRatioByDefault()
    {
        return false;
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
