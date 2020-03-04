<?php

namespace InstagramAPI\Media\Constraints;

use InstagramAPI\Media\ConstraintsInterface;

/**
 * Instagram's story media constraints.
 */
class StoryConstraints implements ConstraintsInterface
{
    /**
     * Lowest allowed story aspect ratio.
     *
     * This range was decided through community research, which revealed that
     * all Instagram stories are in ~9:16 (0.5625, widescreen portrait) ratio,
     * with a small range of similar portrait ratios also being used sometimes.
     *
     * We have selected a photo/video story aspect range which supports all
     * story media aspects that are commonly used by the app: 0.56 - 0.67.
     * (That's ~1080x1611 to ~1080x1928.)
     *
     * However, if you're using our media processing classes, please note that
     * they will target the "best story aspect ratio range" by default, and
     * that you must manually disable that constructor option in the class
     * to get this extended story aspect range, if you REALLY want it...
     *
     * @var float
     *
     * @see https://github.com/mgp25/Instagram-API/issues/1420#issuecomment-318146010
     */
    const MIN_RATIO = 0.56;

    /**
     * Highest allowed story aspect ratio.
     *
     * This range was decided through community research.
     *
     * @var float
     */
    const MAX_RATIO = 0.67;

    /**
     * The recommended story aspect ratio.
     *
     * This is exactly 9:16 ratio, meaning a standard widescreen phone viewed in
     * portrait mode. It is the most common story ratio on Instagram, and it's
     * the one that looks the best on most devices. All other ratios will look
     * "cropped" when viewed on 16:9 widescreen devices, since the app "zooms"
     * the story until it fills the screen without any black bars. So unless the
     * story is exactly 16:9, it won't look great on 16:9 screens.
     *
     * Every manufacturer uses 16:9 screens. Even Apple since the iPhone 5.
     *
     * Therefore, this will be the final target aspect ratio used EVERY time
     * that media destined for a story feed is outside of the allowed range!
     * That's because it doesn't make sense to let people target non-9:16 final
     * story aspect ratios, since only 9:16 stories look good on most devices!
     *
     * @var float
     */
    const RECOMMENDED_RATIO = 0.5625;

    /**
     * The deviation for the recommended aspect ratio.
     *
     * We need to allow a bit above/below it to prevent pointless processing
     * when the media is a few pixels off from the perfect ratio, since the
     * perfect story ratio is often impossible to hit unless the input media
     * is already exactly 720x1280 or 1080x1920.
     *
     * @var float
     */
    const RECOMMENDED_RATIO_DEVIATION = 0.0025;

    /**
     * Minimum allowed video duration.
     *
     * @var float
     */
    const MIN_DURATION = 1.0;

    /**
     * Maximum allowed video duration.
     *
     * @var float
     */
    const MAX_DURATION = 15.0;

    /** {@inheritdoc} */
    public function getTitle()
    {
        return 'story';
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
        return true;
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
