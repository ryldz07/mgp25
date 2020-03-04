<?php

namespace InstagramAPI\Media\Video;

use InstagramAPI\Media\Geometry\Dimensions;
use InstagramAPI\Media\Geometry\Rectangle;
use InstagramAPI\Media\InstagramMedia;
use InstagramAPI\Utils;
use Winbox\Args;

/**
 * Automatically prepares a video file according to Instagram's rules.
 *
 * @property VideoDetails $_details
 */
class InstagramVideo extends InstagramMedia
{
    /** @var FFmpeg */
    protected $_ffmpeg;

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
        parent::__construct($inputFile, $options);
        $this->_details = new VideoDetails($this->_inputFile);

        $this->_ffmpeg = $ffmpeg;
        if ($this->_ffmpeg === null) {
            $this->_ffmpeg = FFmpeg::factory();
        }
    }

    /** {@inheritdoc} */
    protected function _isMod2CanvasRequired()
    {
        return true;
    }

    /** {@inheritdoc} */
    protected function _createOutputFile(
        Rectangle $srcRect,
        Rectangle $dstRect,
        Dimensions $canvas)
    {
        $outputFile = null;

        try {
            // Prepare output file.
            $outputFile = Utils::createTempFile($this->_tmpPath, 'VID');
            // Attempt to process the input file.
            // --------------------------------------------------------------
            // WARNING: This calls ffmpeg, which can run for a long time. The
            // user may be running in a CLI. In that case, if they press Ctrl-C
            // to abort, PHP won't run ANY of our shutdown/destructor handlers!
            // Therefore they'll still have the temp file if they abort ffmpeg
            // conversion with Ctrl-C, since our auto-cleanup won't run. There's
            // absolutely nothing good we can do about that (except a signal
            // handler to interrupt their Ctrl-C, which is a terrible idea).
            // Their OS should clear its temp folder periodically. Or if they
            // use a custom temp folder, it's THEIR own job to clear it!
            // --------------------------------------------------------------
            $this->_processVideo($srcRect, $dstRect, $canvas, $outputFile);
        } catch (\Exception $e) {
            if ($outputFile !== null && is_file($outputFile)) {
                @unlink($outputFile);
            }

            throw $e; // Re-throw.
        }

        return $outputFile;
    }

    /**
     * @param Rectangle  $srcRect    Rectangle to copy from the input.
     * @param Rectangle  $dstRect    Destination place and scale of copied pixels.
     * @param Dimensions $canvas     The size of the destination canvas.
     * @param string     $outputFile
     *
     * @throws \RuntimeException
     */
    protected function _processVideo(
        Rectangle $srcRect,
        Rectangle $dstRect,
        Dimensions $canvas,
        $outputFile)
    {
        // ffmpeg has a bug - https://trac.ffmpeg.org/ticket/6370 - it preserves rotate tag
        // in the output video when the input video has display matrix with rotate
        // and rotation was overridden manually.
        $shouldAutorotate = $this->_ffmpeg->hasNoAutorotate() && $this->_details->hasRotationMatrix();

        // Swap to correct dimensions if the video pixels are stored rotated.
        if (!$shouldAutorotate && $this->_details->hasSwappedAxes()) {
            $srcRect = $srcRect->withSwappedAxes();
            $dstRect = $dstRect->withSwappedAxes();
            $canvas = $canvas->withSwappedAxes();
        }

        // Prepare filters.
        $bgColor = sprintf('0x%02X%02X%02X', ...$this->_bgColor);
        $filters = [
            sprintf('crop=w=%d:h=%d:x=%d:y=%d', $srcRect->getWidth(), $srcRect->getHeight(), $srcRect->getX(), $srcRect->getY()),
            sprintf('scale=w=%d:h=%d', $dstRect->getWidth(), $dstRect->getHeight()),
            sprintf('pad=w=%d:h=%d:x=%d:y=%d:color=%s', $canvas->getWidth(), $canvas->getHeight(), $dstRect->getX(), $dstRect->getY(), $bgColor),
        ];

        // Rotate the video (if needed to).
        $rotationFilters = $this->_getRotationFilters();
        $noAutorotate = false;
        if (!empty($rotationFilters) && !$shouldAutorotate) {
            $filters = array_merge($filters, $rotationFilters);
            $noAutorotate = $this->_ffmpeg->hasNoAutorotate();
        }

        $attempt = 0;
        do {
            ++$attempt;

            // Get the flags to apply to the input file.
            $inputFlags = $this->_getInputFlags($attempt);
            if ($noAutorotate) {
                $inputFlags[] = '-noautorotate';
            }

            // Video format can't copy since we always need to re-encode due to video filtering.
            $ffmpegOutput = $this->_ffmpeg->run(sprintf(
                '-y %s -i %s -vf %s %s %s',
                implode(' ', $inputFlags),
                Args::escape($this->_inputFile),
                Args::escape(implode(',', $filters)),
                implode(' ', $this->_getOutputFlags($attempt)),
                Args::escape($outputFile)
            ));
        } while ($this->_ffmpegMustRunAgain($attempt, $ffmpegOutput));
    }

    /**
     * Internal function to determine whether ffmpeg needs to run again.
     *
     * @param int      $attempt      Which ffmpeg attempt just executed.
     * @param string[] $ffmpegOutput Array of error strings from the attempt.
     *
     * @throws \RuntimeException If this function wants to give up and determines
     *                           that we cannot succeed and should throw completely.
     *
     * @return bool TRUE to run again, FALSE to accept the current output.
     */
    protected function _ffmpegMustRunAgain(
        $attempt,
        array $ffmpegOutput)
    {
        return false;
    }

    /**
     * Get the input flags (placed before the input filename).
     *
     * @param int $attempt The current ffmpeg execution attempt.
     *
     * @return string[]
     */
    protected function _getInputFlags(
        $attempt)
    {
        return [];
    }

    /**
     * Get the output flags (placed before the output filename).
     *
     * @param int $attempt The current ffmpeg execution attempt.
     *
     * @return string[]
     */
    protected function _getOutputFlags(
        $attempt)
    {
        $result = [
            '-metadata:s:v rotate=""', // Strip rotation from metadata.
            '-f mp4', // Force output format to MP4.
        ];

        // Force H.264 for the video.
        $result[] = '-c:v libx264 -preset fast -crf 24';

        // Force AAC for the audio.
        if ($this->_details->getAudioCodec() !== 'aac') {
            if ($this->_ffmpeg->hasLibFdkAac()) {
                $result[] = '-c:a libfdk_aac -vbr 4';
            } else {
                // The encoder 'aac' is experimental but experimental codecs are not enabled,
                // add '-strict -2' if you want to use it.
                $result[] = '-strict -2 -c:a aac -b:a 96k';
            }
        } else {
            $result[] = '-c:a copy';
        }

        // Cut too long videos.
        // FFmpeg cuts video sticking to a closest frame. As a result we might
        // end with a video that is longer than desired duration. To prevent this
        // we must use a duration that is somewhat smaller than its maximum allowed
        // value. 0.1 sec is 1 frame of 10 FPS video.
        $maxDuration = $this->_constraints->getMaxDuration() - 0.1;
        if ($this->_details->getDuration() > $maxDuration) {
            $result[] = sprintf('-t %.2F', $maxDuration);
        }

        // TODO Loop too short videos.
        if ($this->_details->getDuration() < $this->_constraints->getMinDuration()) {
            $times = ceil($this->_constraints->getMinDuration() / $this->_details->getDuration());
        }

        return $result;
    }

    /**
     * Get an array of filters needed to restore video orientation.
     *
     * @return array
     */
    protected function _getRotationFilters()
    {
        $result = [];
        if ($this->_details->hasSwappedAxes()) {
            if ($this->_details->isHorizontallyFlipped() && $this->_details->isVerticallyFlipped()) {
                $result[] = 'transpose=clock';
                $result[] = 'hflip';
            } elseif ($this->_details->isHorizontallyFlipped()) {
                $result[] = 'transpose=clock';
            } elseif ($this->_details->isVerticallyFlipped()) {
                $result[] = 'transpose=cclock';
            } else {
                $result[] = 'transpose=cclock';
                $result[] = 'vflip';
            }
        } else {
            if ($this->_details->isHorizontallyFlipped()) {
                $result[] = 'hflip';
            }
            if ($this->_details->isVerticallyFlipped()) {
                $result[] = 'vflip';
            }
        }

        return $result;
    }
}
