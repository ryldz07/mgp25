<?php

namespace InstagramAPI\Media\Video;

use InstagramAPI\Constants;
use InstagramAPI\Utils;

/**
 * Automatically creates a video thumbnail according to Instagram's rules.
 */
class InstagramThumbnail extends InstagramVideo
{
    /** @var float Thumbnail offset in secs, with milliseconds (decimals). */
    protected $_thumbnailTimestamp;

    /**
     * Constructor.
     *
     * @param string      $inputFile Path to an input file.
     * @param array       $options   An associative array of optional parameters.
     * @param FFmpeg|null $ffmpeg    Custom FFmpeg wrapper.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @see InstagramMedia::__construct() description for the list of parameters.
     */
    public function __construct(
        $inputFile,
        array $options = [],
        FFmpeg $ffmpeg = null)
    {
        parent::__construct($inputFile, $options, $ffmpeg);

        // The timeline and most feeds have the thumbnail at "00:00:01.000".
        $this->_thumbnailTimestamp = 1.0; // Default.

        // Handle per-feed timestamps and custom thumbnail timestamps.
        if (isset($options['targetFeed'])) {
            switch ($options['targetFeed']) {
            case Constants::FEED_STORY:
            case Constants::FEED_DIRECT_STORY:
                // Stories always have the thumbnail at "00:00:00.000" instead.
                $this->_thumbnailTimestamp = 0.0;
                break;
            case Constants::FEED_TIMELINE || Constants::FEED_TIMELINE_ALBUM:
                // Handle custom timestamp (only supported by timeline media).
                // NOTE: Matches real app which only customizes timeline covers.
                if (isset($options['thumbnailTimestamp'])) {
                    $customTimestamp = $options['thumbnailTimestamp'];
                    // If custom timestamp is a number, use as-is. Else assume
                    // a "HH:MM:SS[.000]" string and convert it. Throws if bad.
                    $this->_thumbnailTimestamp = is_int($customTimestamp) || is_float($customTimestamp)
                                               ? (float) $customTimestamp
                                               : Utils::hmsTimeToSeconds($customTimestamp);
                }
                break;
            default:
                // Keep the default.
            }
        }

        // Ensure the timestamp is 0+ and never longer than the video duration.
        if ($this->_thumbnailTimestamp > $this->_details->getDuration()) {
            $this->_thumbnailTimestamp = $this->_details->getDuration();
        }
        if ($this->_thumbnailTimestamp < 0.0) {
            throw new \InvalidArgumentException('Thumbnail timestamp must be a positive number.');
        }
    }

    /**
     * Get thumbnail timestamp as a float.
     *
     * @return float Thumbnail offset in secs, with milliseconds (decimals).
     */
    public function getTimestamp()
    {
        return $this->_thumbnailTimestamp;
    }

    /**
     * Get thumbnail timestamp as a formatted string.
     *
     * @return string The time formatted as `HH:MM:SS.###` (`###` is millis).
     */
    public function getTimestampString()
    {
        return Utils::hmsTimeFromSeconds($this->_thumbnailTimestamp);
    }

    /** {@inheritdoc} */
    protected function _shouldProcess()
    {
        // We must always process the video to get its thumbnail.
        return true;
    }

    /** {@inheritdoc} */
    protected function _ffmpegMustRunAgain(
        $attempt,
        array $ffmpegOutput)
    {
        // If this was the first run, we must look for the "first frame is no
        // keyframe" error. It is a rare error which can happen when the user
        // wants to extract a frame from a timestamp that is before the first
        // keyframe of the video file. Most files can extract frames even at
        // `00:00:00.000`, but certain files may have garbage at the start of
        // the file, and thus extracting a garbage / empty / broken frame and
        // showing this error. The solution is to omit the `-ss` timestamp for
        // such files to automatically make ffmpeg extract the 1st VALID frame.
        // NOTE: We'll only need to retry if timestamp is over 0.0. If it was
        // zero, then we already executed without `-ss` and shouldn't retry.
        if ($attempt === 1 && $this->_thumbnailTimestamp > 0.0) {
            foreach ($ffmpegOutput as $line) {
                // Example: `[flv @ 0x7fc9cc002e00] warning: first frame is no keyframe`.
                if (strpos($line, ': first frame is no keyframe') !== false) {
                    return true;
                }
            }
        }

        // If this was the 2nd run or there was no error, accept result as-is.
        return false;
    }

    /** {@inheritdoc} */
    protected function _getInputFlags(
        $attempt)
    {
        // The seektime *must* be specified here, before the input file.
        // Otherwise ffmpeg will do a slow conversion of the whole file
        // (but discarding converted frames) until it gets to target time.
        // See: https://trac.ffmpeg.org/wiki/Seeking
        // IMPORTANT: WE ONLY APPLY THE SEEK-COMMAND ON THE *FIRST* ATTEMPT. SEE
        // COMMENTS IN `_ffmpegMustRunAgain()` FOR MORE INFORMATION ABOUT WHY.
        // AND WE'LL OMIT SEEKING COMPLETELY IF IT'S "0.0" ("EARLIEST POSSIBLE"), TO
        // GUARANTEE SUCCESS AT GRABBING THE "EARLIEST FRAME" W/O NEEDING RETRIES.
        return $attempt > 1 || $this->_thumbnailTimestamp === 0.0
            ? []
            : [
                sprintf('-ss %s', $this->getTimestampString()),
            ];
    }

    /** {@inheritdoc} */
    protected function _getOutputFlags(
        $attempt)
    {
        return [
            '-f mjpeg',
            '-vframes 1',
        ];
    }
}
