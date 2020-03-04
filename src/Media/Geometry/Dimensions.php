<?php

namespace InstagramAPI\Media\Geometry;

class Dimensions
{
    /** @var int */
    protected $_width;

    /** @var int */
    protected $_height;

    /** @var float */
    protected $_aspectRatio;

    /**
     * Constructor.
     *
     * @param int $width
     * @param int $height
     */
    public function __construct(
        $width,
        $height)
    {
        $this->_width = (int) $width;
        $this->_height = (int) $height;
        // NOTE: MUST `float`-cast to FORCE float even when dividing EQUAL ints.
        $this->_aspectRatio = (float) ($this->_width / $this->_height);
    }

    /**
     * Get stored width for these dimensions.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Get stored height for these dimensions.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * Get stored aspect ratio for these dimensions.
     *
     * @return float
     */
    public function getAspectRatio()
    {
        return $this->_aspectRatio;
    }

    /**
     * Create a new object with swapped axes.
     *
     * @return self
     */
    public function withSwappedAxes()
    {
        return new self($this->_height, $this->_width);
    }

    /**
     * Create a new, scale-adjusted object.
     *
     * @param float|int $newScale     The scale factor to apply.
     * @param string    $roundingFunc One of `round` (default), `floor` or `ceil`.
     *
     * @throws \InvalidArgumentException
     *
     * @return self
     */
    public function withRescaling(
        $newScale = 1.0,
        $roundingFunc = 'round')
    {
        if (!is_float($newScale) && !is_int($newScale)) {
            throw new \InvalidArgumentException('The new scale must be a float or integer.');
        }
        if ($roundingFunc !== 'round' && $roundingFunc !== 'floor' && $roundingFunc !== 'ceil') {
            throw new \InvalidArgumentException(sprintf('Invalid rounding function "%s".', $roundingFunc));
        }

        $newWidth = (int) $roundingFunc($newScale * $this->_width);
        $newHeight = (int) $roundingFunc($newScale * $this->_height);

        return new self($newWidth, $newHeight);
    }
}
