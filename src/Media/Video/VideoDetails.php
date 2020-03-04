<?php

namespace InstagramAPI\Media\Video;

use InstagramAPI\Media\ConstraintsInterface;
use InstagramAPI\Media\MediaDetails;
use InstagramAPI\Utils;
use Winbox\Args;

class VideoDetails extends MediaDetails
{
    /**
     * Minimum allowed video width.
     *
     * These are decided by Instagram. Not by us!
     *
     * This value is the same for both stories and general media.
     *
     * @var int
     */
    const MIN_WIDTH = 480;

    /**
     * Maximum allowed video width.
     *
     * These are decided by Instagram. Not by us!
     *
     * This value is the same for both stories and general media.
     *
     * @var int
     */
    const MAX_WIDTH = 720;

    /** @var float */
    private $_duration;

    /** @var string */
    private $_videoCodec;

    /** @var string|null */
    private $_audioCodec;

    /** @var string */
    private $_container;

    /** @var int */
    private $_rotation;

    /** @var bool */
    private $_hasRotationMatrix;

    /**
     * @return float
     */
    public function getDuration()
    {
        return $this->_duration;
    }

    /**
     * @return int
     */
    public function getDurationInMsec()
    {
        // NOTE: ceil() is to round up and get rid of any MS decimals.
        return (int) ceil($this->getDuration() * 1000);
    }

    /**
     * @return string
     */
    public function getVideoCodec()
    {
        return $this->_videoCodec;
    }

    /**
     * @return string|null
     */
    public function getAudioCodec()
    {
        return $this->_audioCodec;
    }

    /**
     * @return string
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /** {@inheritdoc} */
    public function hasSwappedAxes()
    {
        return $this->_rotation % 180;
    }

    /** {@inheritdoc} */
    public function isHorizontallyFlipped()
    {
        return $this->_rotation === 90 || $this->_rotation === 180;
    }

    /** {@inheritdoc} */
    public function isVerticallyFlipped()
    {
        return $this->_rotation === 180 || $this->_rotation === 270;
    }

    /**
     * Check whether the video has display matrix with rotate.
     *
     * @return bool
     */
    public function hasRotationMatrix()
    {
        return $this->_hasRotationMatrix;
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
     * @param string $filename Path to the video file.
     *
     * @throws \InvalidArgumentException If the video file is missing or invalid.
     * @throws \RuntimeException         If FFmpeg isn't working properly.
     */
    public function __construct(
        $filename)
    {
        // Check if input file exists.
        if (empty($filename) || !is_file($filename)) {
            throw new \InvalidArgumentException(sprintf('The video file "%s" does not exist on disk.', $filename));
        }

        // Determine video file size and throw when the file is empty.
        $filesize = filesize($filename);
        if ($filesize < 1) {
            throw new \InvalidArgumentException(sprintf(
                'The video "%s" is empty.',
                $filename
            ));
        }

        // The user must have FFprobe.
        $ffprobe = Utils::checkFFPROBE();
        if ($ffprobe === false) {
            throw new \RuntimeException('You must have FFprobe to analyze video details. Ensure that its binary-folder exists in your PATH environment variable, or manually set its full path via "\InstagramAPI\Utils::$ffprobeBin = \'/home/exampleuser/ffmpeg/bin/ffprobe\';" at the start of your script.');
        }

        // Load with FFPROBE. Shows details as JSON and exits.
        $command = sprintf(
            '%s -v quiet -print_format json -show_format -show_streams %s',
            Args::escape($ffprobe),
            Args::escape($filename)
        );
        $jsonInfo = @shell_exec($command);

        // Check for processing errors.
        if ($jsonInfo === null) {
            throw new \RuntimeException(sprintf('FFprobe failed to analyze your video file "%s".', $filename));
        }

        // Attempt to decode the JSON.
        $probeResult = @json_decode($jsonInfo, true);
        if ($probeResult === null) {
            throw new \RuntimeException(sprintf('FFprobe gave us invalid JSON for "%s".', $filename));
        }

        if (!isset($probeResult['streams']) || !is_array($probeResult['streams'])) {
            throw new \RuntimeException(sprintf('FFprobe failed to detect any stream. Is "%s" a valid media file?', $filename));
        }

        // Now analyze all streams to find the first video stream.
        $width = null;
        $height = null;
        $this->_hasRotationMatrix = false;
        foreach ($probeResult['streams'] as $streamIdx => $streamInfo) {
            if (!isset($streamInfo['codec_type'])) {
                continue;
            }
            switch ($streamInfo['codec_type']) {
                case 'video':
                    // TODO Mark media as invalid if more than one video stream found (?)
                    $foundVideoStream = true;
                    $this->_videoCodec = (string) $streamInfo['codec_name']; // string
                    $width = (int) $streamInfo['width'];
                    $height = (int) $streamInfo['height'];
                    if (isset($streamInfo['duration'])) {
                        // NOTE: Duration is a float such as "230.138000".
                        $this->_duration = (float) $streamInfo['duration'];
                    }

                    // Read rotation angle from tags.
                    $this->_rotation = 0;
                    if (isset($streamInfo['tags']) && is_array($streamInfo['tags'])) {
                        $tags = array_change_key_case($streamInfo['tags'], CASE_LOWER);
                        if (isset($tags['rotate'])) {
                            $this->_rotation = $this->_normalizeRotation((int) $tags['rotate']);
                        }
                    }
                    if (isset($streamInfo['side_data_list']) && is_array($streamInfo['side_data_list'])) {
                        foreach ($streamInfo['side_data_list'] as $sideData) {
                            if (!isset($sideData['side_data_type'])) {
                                continue;
                            }
                            if (!isset($sideData['rotation'])) {
                                continue;
                            }

                            $this->_hasRotationMatrix =
                                strcasecmp($sideData['side_data_type'], 'Display Matrix') === 0
                                && abs($sideData['rotation']) > 0.1;
                        }
                    }
                    break;
                case 'audio':
                    // TODO Mark media as invalid if more than one audio stream found (?)
                    $this->_audioCodec = (string) $streamInfo['codec_name']; // string
                    break;
                default:
                    // TODO Mark media as invalid if unknown stream found (?)
            }
        }

        // Sometimes there is no duration in stream info, so we should check the format.
        if ($this->_duration === null && isset($probeResult['format']['duration'])) {
            $this->_duration = (float) $probeResult['format']['duration'];
        }

        if (isset($probeResult['format']['format_name'])) {
            $this->_container = (string) $probeResult['format']['format_name'];
        }

        // Make sure we have detected the video duration.
        if ($this->_duration === null) {
            throw new \RuntimeException(sprintf('FFprobe failed to detect video duration. Is "%s" a valid video file?', $filename));
        }

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

        // Make sure we have found at least one video stream.
        if ($this->_videoCodec === null) {
            throw new \InvalidArgumentException(sprintf(
                'Instagram requires video with at least one video stream. Your file "%s" doesn\'t have any.',
                $mediaFilename
            ));
        }

        // Check the video stream. We should have at least one.
        if ($this->_videoCodec !== 'h264') {
            throw new \InvalidArgumentException(sprintf(
                'Instagram only accepts videos encoded with the H.264 video codec. Your file "%s" has "%s".',
                $mediaFilename, $this->_videoCodec
            ));
        }

        // Check the audio stream (if available).
        if ($this->_audioCodec !== null && $this->_audioCodec !== 'aac') {
            throw new \InvalidArgumentException(sprintf(
                'Instagram only accepts videos encoded with the AAC audio codec. Your file "%s" has "%s".',
                $mediaFilename, $this->_audioCodec
            ));
        }

        // Check the container format.
        if (strpos($this->_container, 'mp4') === false) {
            throw new \InvalidArgumentException(sprintf(
                'Instagram only accepts videos packed into MP4 containers. Your file "%s" has "%s".',
                $mediaFilename, $this->_container
            ));
        }

        // Validate video resolution. Instagram allows between 480px-720px width.
        // NOTE: They'll resize 720px wide videos on the server, to 640px instead.
        // NOTE: Their server CAN receive between 320px-1080px width without
        // rejecting the video, but the official app would NEVER upload such
        // resolutions. It's controlled by the "ig_android_universe_video_production"
        // experiment variable, which currently enforces width of min:480, max:720.
        // If users want to upload bigger videos, they MUST resize locally first!
        $width = $this->getWidth();
        if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
            throw new \InvalidArgumentException(sprintf(
                'Instagram only accepts videos that are between %d and %d pixels wide. Your file "%s" is %d pixels wide.',
                self::MIN_WIDTH, self::MAX_WIDTH, $mediaFilename, $width
            ));
        }

        // Validate video length.
        // NOTE: Instagram has no disk size limit, but this length validation
        // also ensures we can only upload small files exactly as intended.
        $duration = $this->getDuration();
        $minDuration = $constraints->getMinDuration();
        $maxDuration = $constraints->getMaxDuration();
        if ($duration < $minDuration || $duration > $maxDuration) {
            throw new \InvalidArgumentException(sprintf(
                'Instagram only accepts %s videos that are between %.3f and %.3f seconds long. Your video "%s" is %.3f seconds long.',
                $constraints->getTitle(), $minDuration, $maxDuration, $mediaFilename, $duration
            ));
        }
    }

    /**
     * @param $rotation
     *
     * @return int
     */
    private function _normalizeRotation(
        $rotation)
    {
        // The angle must be in 0..359 degrees range.
        $result = $rotation % 360;
        // Negative angle can be normalized by adding it to 360:
        // 360 + (-90) = 270.
        if ($result < 0) {
            $result = 360 + $result;
        }
        // The final angle must be one of 0, 90, 180 or 270 degrees.
        // So we are rounding it to the closest one.
        $result = round($result / 90) * 90;

        return (int) $result;
    }
}
