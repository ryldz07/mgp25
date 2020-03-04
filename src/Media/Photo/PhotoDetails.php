<?php

namespace InstagramAPI\Media\Photo;

use InstagramAPI\Media\ConstraintsInterface;
use InstagramAPI\Media\MediaDetails;

class PhotoDetails extends MediaDetails
{
    /**
     * Minimum allowed image width.
     *
     * These are decided by Instagram. Not by us!
     *
     * This value is the same for both stories and general media.
     *
     * @var int
     *
     * @see https://help.instagram.com/1631821640426723
     */
    const MIN_WIDTH = 320;

    /**
     * Maximum allowed image width.
     *
     * These are decided by Instagram. Not by us!
     *
     * This value is the same for both stories and general media.
     *
     * Note that Instagram doesn't enforce any max-height. Instead, it checks
     * the width and aspect ratio which ensures that the height is legal too.
     *
     * @var int
     */
    const MAX_WIDTH = 1080;

    /**
     * Default orientation to use if no EXIF JPG orientation exists.
     *
     * This value represents a non-rotated, non-flipped image.
     *
     * @var int
     */
    const DEFAULT_ORIENTATION = 1;

    /** @var int */
    private $_type;

    /** @var int */
    private $_orientation;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->_type;
    }

    /** {@inheritdoc} */
    public function hasSwappedAxes()
    {
        return in_array($this->_orientation, [5, 6, 7, 8], true);
    }

    /** {@inheritdoc} */
    public function isHorizontallyFlipped()
    {
        return in_array($this->_orientation, [2, 3, 6, 7], true);
    }

    /** {@inheritdoc} */
    public function isVerticallyFlipped()
    {
        return in_array($this->_orientation, [3, 4, 7, 8], true);
    }

    /** {@inheritdoc} */
    public function getMinAllowedWidth()
    {
        return self::MIN_WIDTH;
    }

    /** {@inheritdoc} */
    public function getMaxAllowedWidth()
    {
        return self::MAX_WIDTH;
    }

    /**
     * Constructor.
     *
     * @param string $filename Path to the photo file.
     *
     * @throws \InvalidArgumentException If the photo file is missing or invalid.
     */
    public function __construct(
        $filename)
    {
        // Check if input file exists.
        if (empty($filename) || !is_file($filename)) {
            throw new \InvalidArgumentException(sprintf('The photo file "%s" does not exist on disk.', $filename));
        }

        // Determine photo file size and throw when the file is empty.
        $filesize = filesize($filename);
        if ($filesize < 1) {
            throw new \InvalidArgumentException(sprintf(
                'The photo file "%s" is empty.',
                $filename
            ));
        }

        // Get image details.
        $result = @getimagesize($filename);
        if ($result === false) {
            throw new \InvalidArgumentException(sprintf('The photo file "%s" is not a valid image.', $filename));
        }
        list($width, $height, $this->_type) = $result;

        // Detect JPEG EXIF orientation if it exists.
        $this->_orientation = $this->_getExifOrientation($filename, $this->_type);

        parent::__construct($filename, $filesize, $width, $height);
    }

    /** {@inheritdoc} */
    public function validate(
        ConstraintsInterface $constraints)
    {
        parent::validate($constraints);

        // WARNING TO CONTRIBUTORS: $mediaFilename is for ERROR DISPLAY to
        // users. Do NOT use it to read from the hard disk!
        $mediaFilename = $this->getBasename();

        // Validate image type.
        // NOTE: It is confirmed that Instagram only accepts JPEG files.
        $type = $this->getType();
        if ($type !== IMAGETYPE_JPEG) {
            throw new \InvalidArgumentException(sprintf('The photo file "%s" is not a JPEG file.', $mediaFilename));
        }

        $width = $this->getWidth();
        // Validate photo resolution. Instagram allows between 320px-1080px width.
        if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
            throw new \InvalidArgumentException(sprintf(
                'Instagram only accepts photos that are between %d and %d pixels wide. Your file "%s" is %d pixels wide.',
                self::MIN_WIDTH, self::MAX_WIDTH, $mediaFilename, $width
            ));
        }
    }

    /**
     * Get the EXIF orientation from given file.
     *
     * @param string $filename
     * @param int    $type
     *
     * @return int
     */
    protected function _getExifOrientation(
        $filename,
        $type)
    {
        if ($type !== IMAGETYPE_JPEG || !function_exists('exif_read_data')) {
            return self::DEFAULT_ORIENTATION;
        }

        $exif = @exif_read_data($filename);
        if ($exif === false || !isset($exif['Orientation'])) {
            return self::DEFAULT_ORIENTATION;
        }

        return (int) $exif['Orientation'];
    }
}
