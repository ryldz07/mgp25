<?php

namespace InstagramAPI\Media\Video;

use InstagramAPI\Utils;
use Symfony\Component\Process\Process;
use Winbox\Args;

class FFmpeg
{
    const BINARIES = [
        'ffmpeg',
        'avconv',
    ];

    const WINDOWS_BINARIES = [
        'ffmpeg.exe',
        'avconv.exe',
    ];

    /** @var string|null */
    public static $defaultBinary;

    /** @var int|null */
    public static $defaultTimeout;

    /** @var FFmpeg[] */
    protected static $_instances = [];

    /** @var string */
    protected $_ffmpegBinary;

    /** @var bool */
    protected $_hasNoAutorotate;

    /** @var bool */
    protected $_hasLibFdkAac;

    /**
     * Constructor.
     *
     * @param string $ffmpegBinary
     *
     * @throws \RuntimeException When a ffmpeg binary is missing.
     */
    protected function __construct(
        $ffmpegBinary)
    {
        $this->_ffmpegBinary = $ffmpegBinary;

        try {
            $this->version();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('It seems that the path to ffmpeg binary is invalid. Please check your path to ensure that it is correct.'));
        }
    }

    /**
     * Create a new instance or use a cached one.
     *
     * @param string|null $ffmpegBinary Path to a ffmpeg binary, or NULL to autodetect.
     *
     * @return static
     */
    public static function factory(
        $ffmpegBinary = null)
    {
        if ($ffmpegBinary === null) {
            return static::_autoDetectBinary();
        }

        if (isset(self::$_instances[$ffmpegBinary])) {
            return self::$_instances[$ffmpegBinary];
        }

        $instance = new static($ffmpegBinary);
        self::$_instances[$ffmpegBinary] = $instance;

        return $instance;
    }

    /**
     * Run a command and wrap errors into an Exception (if any).
     *
     * @param string $command
     *
     * @throws \RuntimeException
     *
     * @return string[]
     */
    public function run(
        $command)
    {
        $process = $this->runAsync($command);

        try {
            $exitCode = $process->wait();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Failed to run the ffmpeg binary: %s', $e->getMessage()));
        }
        if ($exitCode) {
            $errors = preg_replace('#[\r\n]+#', '"], ["', trim($process->getErrorOutput()));
            $errorMsg = sprintf('FFmpeg Errors: ["%s"], Command: "%s".', $errors, $command);

            throw new \RuntimeException($errorMsg, $exitCode);
        }

        return preg_split('#[\r\n]+#', $process->getOutput(), null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Run a command asynchronously.
     *
     * @param string $command
     *
     * @return Process
     */
    public function runAsync(
        $command)
    {
        $fullCommand = sprintf('%s -v error %s', Args::escape($this->_ffmpegBinary), $command);

        $process = new Process($fullCommand);
        if (is_int(self::$defaultTimeout) && self::$defaultTimeout > 60) {
            $process->setTimeout(self::$defaultTimeout);
        }
        $process->start();

        return $process;
    }

    /**
     * Get the ffmpeg version.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function version()
    {
        return $this->run('-version')[0];
    }

    /**
     * Get the path to the ffmpeg binary.
     *
     * @return string
     */
    public function getFFmpegBinary()
    {
        return $this->_ffmpegBinary;
    }

    /**
     * Check whether ffmpeg has -noautorotate flag.
     *
     * @return bool
     */
    public function hasNoAutorotate()
    {
        if ($this->_hasNoAutorotate === null) {
            try {
                $this->run('-noautorotate -f lavfi -i color=color=red -t 1 -f null -');
                $this->_hasNoAutorotate = true;
            } catch (\RuntimeException $e) {
                $this->_hasNoAutorotate = false;
            }
        }

        return $this->_hasNoAutorotate;
    }

    /**
     * Check whether ffmpeg has libfdk_aac audio encoder.
     *
     * @return bool
     */
    public function hasLibFdkAac()
    {
        if ($this->_hasLibFdkAac === null) {
            $this->_hasLibFdkAac = $this->_hasAudioEncoder('libfdk_aac');
        }

        return $this->_hasLibFdkAac;
    }

    /**
     * Check whether ffmpeg has specified audio encoder.
     *
     * @param string $encoder
     *
     * @return bool
     */
    protected function _hasAudioEncoder(
        $encoder)
    {
        try {
            $this->run(sprintf(
                '-f lavfi -i anullsrc=channel_layout=stereo:sample_rate=44100 -c:a %s -t 1 -f null -',
                Args::escape($encoder)
            ));

            return true;
        } catch (\RuntimeException $e) {
            return false;
        }
    }

    /**
     * @throws \RuntimeException
     *
     * @return static
     */
    protected static function _autoDetectBinary()
    {
        $binaries = defined('PHP_WINDOWS_VERSION_MAJOR') ? self::WINDOWS_BINARIES : self::BINARIES;
        if (self::$defaultBinary !== null) {
            array_unshift($binaries, self::$defaultBinary);
        }
        /* Backwards compatibility. */
        if (Utils::$ffmpegBin !== null) {
            array_unshift($binaries, Utils::$ffmpegBin);
        }

        $instance = null;
        foreach ($binaries as $binary) {
            if (isset(self::$_instances[$binary])) {
                return self::$_instances[$binary];
            }

            try {
                $instance = new static($binary);
            } catch (\Exception $e) {
                continue;
            }
            self::$defaultBinary = $binary;
            self::$_instances[$binary] = $instance;

            return $instance;
        }

        throw new \RuntimeException('You must have FFmpeg to process videos. Ensure that its binary-folder exists in your PATH environment variable, or manually set its full path via "\InstagramAPI\Media\Video\FFmpeg::$defaultBinary = \'/home/exampleuser/ffmpeg/bin/ffmpeg\';" at the start of your script.');
    }
}
