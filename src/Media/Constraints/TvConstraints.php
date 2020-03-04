<?php

namespace InstagramAPI\Media\Constraints;

/**
 * Instagram's Tv media constraints.
 */
class TvConstraints extends StoryConstraints
{
    /**
     * Lowest allowed aspect ratio.
     *
     * It is controlled by ig_android_igtv_aspect_ratio_limits/min_aspect_ratio experiment.
     *
     * // TODO Use the experiment.
     *
     * @var float
     */
    const MIN_RATIO = 0.5;

    /**
     * Highest allowed aspect ratio.
     *
     * It is controlled by ig_android_igtv_aspect_ratio_limits/max_aspect_ratio experiment.
     *
     * // TODO Use the experiment.
     *
     * @var float
     */
    const MAX_RATIO = 0.8;

    /**
     * Minimum allowed video duration.
     *
     * It is controlled by ig_android_felix_video_upload_length/minimum_duration experiment.
     *
     * // TODO Use the experiment.
     *
     * @var float
     */
    const MIN_DURATION = 15.0;

    /**
     * Maximum allowed video duration.
     *
     * It is controlled by ig_android_felix_video_upload_length/maximum_duration experiment.
     *
     * // TODO Use the experiment.
     *
     * @var float
     */
    const MAX_DURATION = 600.0;

    /** {@inheritdoc} */
    public function getTitle()
    {
        return 'TV';
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
