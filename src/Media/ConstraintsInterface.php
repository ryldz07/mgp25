<?php

namespace InstagramAPI\Media;

/**
 * Represents Instagram's media constraints.
 */
interface ConstraintsInterface
{
    /**
     * Get the title for exception messages.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get the minimum allowed media aspect ratio.
     *
     * @return float
     */
    public function getMinAspectRatio();

    /**
     * Get the maximum allowed media aspect ratio.
     *
     * @return float
     */
    public function getMaxAspectRatio();

    /**
     * Get the recommended media aspect ratio.
     *
     * @return float
     */
    public function getRecommendedRatio();

    /**
     * Get the deviation for recommended media aspect ratio.
     *
     * @return float
     */
    public function getRecommendedRatioDeviation();

    /**
     * Whether to use the recommended media aspect ratio by default.
     *
     * @return bool
     */
    public function useRecommendedRatioByDefault();

    /**
     * Get the minimum allowed video duration.
     *
     * @return float
     */
    public function getMinDuration();

    /**
     * Get the maximum allowed video duration.
     *
     * @return float
     */
    public function getMaxDuration();
}
