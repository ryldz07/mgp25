<?php

namespace InstagramAPI\Media;

use InstagramAPI\Constants;
use InstagramAPI\Media\Constraints\ConstraintsFactory;
use InstagramAPI\Media\Geometry\Dimensions;
use InstagramAPI\Media\Geometry\Rectangle;

/**
 * Automatically prepares a media file according to Instagram's rules.
 *
 * Validates, transcodes, resizes and crops/expands a media file to match
 * Instagram's requirements, if necessary. You can also use this with your own
 * parameters, to force your media into different aspects, ie square, or for
 * adding colored borders to media, and so on... Read the constructor options!
 *
 * Usage:
 *
 * - Create an instance of the appropriate subclass (such as `InstagramPhoto` or
 *   `InstagramVideo`) with your media file and requirements.
 * - Call `getFile()` to get the path to a media file matching the requirements.
 *   This will be the same as the input file if no processing was required.
 * - Optionally, call `deleteFile()` if you want to delete the temporary file
 *   ahead of time instead of automatically when PHP does its object garbage
 *   collection. This function is safe and won't delete the original input file.
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
abstract class InstagramMedia
{
    /** @var int Crop Operation. */
    const CROP = 1;

    /** @var int Expand Operation. */
    const EXPAND = 2;

    /**
     * Override for the default temp path used by all class instances.
     *
     * If you don't provide any tmpPath to the constructor, we'll use this value
     * instead (if non-null). Otherwise we'll use the default system tmp folder.
     *
     * TIP: If your default system temp folder isn't writable, it's NECESSARY
     * for you to set this value to another, writable path, like this:
     *
     * ```
     * \InstagramAPI\InstagramMedia::$defaultTmpPath = '/home/example/foo/';
     * ```
     *
     * @var string|null
     */
    public static $defaultTmpPath = null;

    /** @var bool Whether to output debugging info during calculation steps. */
    protected $_debug;

    /** @var string Input file path. */
    protected $_inputFile;

    /** @var float|null Minimum allowed aspect ratio. */
    protected $_minAspectRatio;

    /** @var float|null Maximum allowed aspect ratio. */
    protected $_maxAspectRatio;

    /** @var float Whether to allow the new aspect ratio (during processing) to
     * deviate slightly from the min/max targets. See constructor for info. */
    protected $_allowNewAspectDeviation;

    /** @var int Crop focus position (-50 .. 50) when cropping horizontally. */
    protected $_horCropFocus;

    /** @var int Crop focus position (-50 .. 50) when cropping vertically. */
    protected $_verCropFocus;

    /** @var array Background color [R, G, B] for the final media. */
    protected $_bgColor;

    /** @var bool Whether to use the media as a blurred border. */
    protected $_blurredBorder;

    /** @var int Operation to perform on the media. */
    protected $_operation;

    /** @var string Path to a tmp directory. */
    protected $_tmpPath;

    /** @var ConstraintsInterface Target feed's specific constraints. */
    protected $_constraints;

    /** @var string Output file path. */
    protected $_outputFile;

    /** @var MediaDetails The media details for our input file. */
    protected $_details;

    /** @var float|null Optional forced aspect ratio target to apply in case of
     * input being outside allowed min/max range (OR if the input deviates too
     * much from this target, in case this target ratio was user-provided). */
    protected $_forceTargetAspectRatio;

    /** @var bool Whether the user specified the "forced target ratio"
     * themselves, which means we should be VERY strict about applying it. */
    protected $_hasUserForceTargetAspectRatio;

    /**
     * Constructor.
     *
     * Available `$options` parameters:
     *
     * - "targetFeed" (int): One of the FEED_X constants. MUST be used if you're
     *   targeting stories. Defaults to `Constants::FEED_TIMELINE`.
     *
     * - "horCropFocus" (int): Crop focus position (-50 .. 50) when cropping
     *   horizontally (reducing width). Uses intelligent guess if not set.
     *
     * - "verCropFocus" (int): Crop focus position (-50 .. 50) when cropping
     *   vertically (reducing height). Uses intelligent guess if not set.
     *
     * - "minAspectRatio" (float): Minimum allowed aspect ratio. Uses
     *   auto-selected class constants (with the correct, legal Instagram
     *   aspect ratio limits for your chosen target feed) if not set.
     *
     * - "maxAspectRatio" (float): Maximum allowed aspect ratio. Uses
     *   auto-selected class constants (with the correct, legal Instagram
     *   aspect ratio limits for your chosen target feed) if not set.
     *
     * - "forceAspectRatio" (float|int): Tell the media processor to enforce a
     *   specific aspect ratio target. This custom value MUST be within the
     *   "minAspectRatio" to "maxAspectRatio" range! NOTE: When your goal is to
     *   generate a custom media aspect ratio, you should normally ONLY specify
     *   THIS parameter and should NEVER tamper with the min/max aspect ratio
     *   limits! Because when you're specifying a custom forced ratio here, we
     *   WILL ALWAYS verify/process the media as-necessary to fit your desired
     *   ratio (EVEN if the input media was "already valid" within the overall,
     *   larger legal min/max ratio ranges). ALSO NOTE: We WON'T process input
     *   media when it's already VERY close to the desired target ratio. The
     *   ONLY custom aspect ratio value that is 100% guaranteed to be FULLY
     *   ENFORCED is `1.0` (square). If you specify ANY other ratio, we will
     *   accept a VERY SMALL deviation when the media is already almost exactly
     *   at the desired ratio. This is done to prevent pointless "impossible
     *   processing", since achieving EXACT non-`1.0` ratios is almost always
     *   IMPOSSIBLE, so it's not worth trying with media that's already very
     *   close. The reason why non-`1.0` ratios are impossibly hard to hit in
     *   MOST cases, is because pixels cannot be subdivided into smaller
     *   fractions than "1 whole pixel", so in most cases when we ask for
     *   something like `1.25385` ratio and we have an input file such as
     *   `640x512` (`1.25` ratio), it is PHYSICALLY IMPOSSIBLE to achieve a
     *   `1.25385` ratio with that input and we could AT BEST reach a tiny
     *   fraction away from that target; such as `641x512` (`1.251953125`
     *   ratio), `639x512` (`1.248046875` ratio), `640x513` (`1.247563353`
     *   ratio) or `640x511` (`1.252446184` ratio). So whether you CAN hit your
     *   EXACT desired non-`1.0` ratio or NOT depends 100% on your input file's
     *   resolution! That's why we passthrough files that are already super
     *   close to the desired ratio. Either way, such a tiny difference in
     *   aspect ratio won't be visible to the eye, so if you are using this
     *   "target ratio" feature for creating Instagram albums where all media
     *   has the same aspect ratio, the final result WILL still look perfect.
     *
     * - "useRecommendedRatio" (bool): Whether to use the recommended aspect
     *   ratio for your specific media type and target feed (such as using an
     *   appropriate `9:16` portrait widescreen for stories). Some targets use
     *   the recommended ratio by default, and others disable this by default.
     *   Therefore, you do NOT need to set this option manually unless you have
     *   a VERY special reason to do so! NOTE: This will ALWAYS be set to
     *   `FALSE` if you're using the "forceAspectRatio" parameter!
     *
     * - "allowNewAspectDeviation" (bool): Whether to allow the new, final
     *   aspect ratio (during processing) to deviate slightly from the MIN/MAX
     *   limits. By setting this option to `TRUE`, you will tell our processing
     *   to allow "slightly too high/slightly too low" final aspect ratios
     *   (instead of throwing an exception) and to still permit the final
     *   "closest-possible canvas" we've calculated anyway. You should NEVER
     *   need to use this feature, but if you have manually configured extremely
     *   narrow min/max aspect ratio parameters (which your input media CANNOT
     *   be tweaked to fit perfectly within) then you MAY want to enable this.
     *
     * - "bgColor" (array) - Array with 3 color components `[R, G, B]`
     *   (0-255/0x00-0xFF) for the background. Uses white if not set.
     *
     * - "blurredBorder" (bool) - Whether to use the media as a blurred border.
     *   False if not set.
     *
     * - "operation" (int) - Operation to perform on the media (CROP or EXPAND).
     *   Uses `self::CROP` if not set.
     *
     * - "tmpPath" (string) - Path to temp directory. Uses system temp location
     *   or the class-default (`self::$defaultTmpPath`) if not set.
     *
     * - "debug" (bool) - Whether to output debugging info during calculation
     *   steps.
     *
     * @param string $inputFile Path to an input file.
     * @param array  $options   An associative array of optional parameters.
     *                          See constructor description.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $inputFile,
        array $options = [])
    {
        // Assign variables for all options, to avoid bulky code repetition.
        $targetFeed = isset($options['targetFeed']) ? $options['targetFeed'] : Constants::FEED_TIMELINE;
        $horCropFocus = isset($options['horCropFocus']) ? $options['horCropFocus'] : null;
        $verCropFocus = isset($options['verCropFocus']) ? $options['verCropFocus'] : null;
        $minAspectRatio = isset($options['minAspectRatio']) ? $options['minAspectRatio'] : null;
        $maxAspectRatio = isset($options['maxAspectRatio']) ? $options['maxAspectRatio'] : null;
        $userForceTargetAspectRatio = isset($options['forceAspectRatio']) ? $options['forceAspectRatio'] : null;
        $useRecommendedRatio = isset($options['useRecommendedRatio']) ? (bool) $options['useRecommendedRatio'] : null;
        $allowNewAspectDeviation = isset($options['allowNewAspectDeviation']) ? (bool) $options['allowNewAspectDeviation'] : false;
        $bgColor = isset($options['bgColor']) ? $options['bgColor'] : null;
        $blurredBorder = isset($options['blurredBorder']) ? (bool) $options['blurredBorder'] : false;
        $operation = isset($options['operation']) ? $options['operation'] : self::CROP;
        $tmpPath = isset($options['tmpPath']) ? (string) $options['tmpPath'] : null;
        $debug = isset($options['debug']) ? $options['debug'] : false;

        // Debugging.
        $this->_debug = $debug === true;

        // Input file.
        if (!is_file($inputFile)) {
            throw new \InvalidArgumentException(sprintf('Input file "%s" doesn\'t exist.', $inputFile));
        }
        $this->_inputFile = $inputFile;

        // Horizontal crop focus.
        if ($horCropFocus !== null && (!is_int($horCropFocus) || $horCropFocus < -50 || $horCropFocus > 50)) {
            throw new \InvalidArgumentException('Horizontal crop focus must be between -50 and 50.');
        }
        $this->_horCropFocus = $horCropFocus;

        // Vertical crop focus.
        if ($verCropFocus !== null && (!is_int($verCropFocus) || $verCropFocus < -50 || $verCropFocus > 50)) {
            throw new \InvalidArgumentException('Vertical crop focus must be between -50 and 50.');
        }
        $this->_verCropFocus = $verCropFocus;

        // Minimum and maximum aspect ratio range.
        if ($minAspectRatio !== null && !is_float($minAspectRatio)) {
            throw new \InvalidArgumentException('Minimum aspect ratio must be a floating point number.');
        }
        if ($maxAspectRatio !== null && !is_float($maxAspectRatio)) {
            throw new \InvalidArgumentException('Maximum aspect ratio must be a floating point number.');
        }

        // Does the user want to override (force) the final "target aspect ratio" choice?
        // NOTE: This will be used to override `$this->_forceTargetAspectRatio`.
        $this->_hasUserForceTargetAspectRatio = false;
        if ($userForceTargetAspectRatio !== null) {
            if (!is_float($userForceTargetAspectRatio) && !is_int($userForceTargetAspectRatio)) {
                throw new \InvalidArgumentException('Custom target aspect ratio must be a float or integer.');
            }
            $userForceTargetAspectRatio = (float) $userForceTargetAspectRatio;
            $this->_hasUserForceTargetAspectRatio = true;
            $useRecommendedRatio = false; // We forcibly disable this too, to avoid risk of future bugs.
        }

        // Create constraints and determine whether to use "recommended target aspect ratio" (if one is available for feed).
        $this->_constraints = ConstraintsFactory::createFor($targetFeed);
        if (!$this->_hasUserForceTargetAspectRatio && $useRecommendedRatio === null) {
            // No value is provided, so let's guess it.
            if ($minAspectRatio !== null || $maxAspectRatio !== null) {
                // If we have at least one custom ratio, we must not use recommended ratio.
                $useRecommendedRatio = false;
            } else {
                // Use the recommended value from constraints (either on or off, depending on which target feed).
                $useRecommendedRatio = $this->_constraints->useRecommendedRatioByDefault();
            }
        }

        // Determine the legal min/max aspect ratios for the target feed.
        if (!$this->_hasUserForceTargetAspectRatio && $useRecommendedRatio === true) {
            $this->_forceTargetAspectRatio = $this->_constraints->getRecommendedRatio();
            $deviation = $this->_constraints->getRecommendedRatioDeviation();
            $minAspectRatio = $this->_forceTargetAspectRatio - $deviation;
            $maxAspectRatio = $this->_forceTargetAspectRatio + $deviation;
        } else {
            // If the user hasn't specified a custom target aspect ratio, this
            // "force" value will remain NULL (and the target ratio will be
            // auto-calculated by the canvas generation algorithms instead).
            $this->_forceTargetAspectRatio = $userForceTargetAspectRatio;
            $allowedMinRatio = $this->_constraints->getMinAspectRatio();
            $allowedMaxRatio = $this->_constraints->getMaxAspectRatio();

            // Select allowed aspect ratio range based on defaults and user input.
            if ($minAspectRatio !== null && ($minAspectRatio < $allowedMinRatio || $minAspectRatio > $allowedMaxRatio)) {
                throw new \InvalidArgumentException(sprintf('Minimum aspect ratio must be between %.3f and %.3f.',
                    $allowedMinRatio, $allowedMaxRatio));
            }
            if ($minAspectRatio === null) {
                $minAspectRatio = $allowedMinRatio;
            }
            if ($maxAspectRatio !== null && ($maxAspectRatio < $allowedMinRatio || $maxAspectRatio > $allowedMaxRatio)) {
                throw new \InvalidArgumentException(sprintf('Maximum aspect ratio must be between %.3f and %.3f.',
                    $allowedMinRatio, $allowedMaxRatio));
            }
            if ($maxAspectRatio === null) {
                $maxAspectRatio = $allowedMaxRatio;
            }
            if ($minAspectRatio !== null && $maxAspectRatio !== null && $minAspectRatio > $maxAspectRatio) {
                throw new \InvalidArgumentException('Maximum aspect ratio must be greater than or equal to minimum.');
            }

            // Validate custom target aspect ratio legality if provided by user.
            if ($this->_hasUserForceTargetAspectRatio) {
                if ($minAspectRatio !== null && $this->_forceTargetAspectRatio < $minAspectRatio) {
                    throw new \InvalidArgumentException(sprintf('Custom target aspect ratio (%.5f) must be greater than or equal to the minimum aspect ratio (%.5f).',
                                                                $this->_forceTargetAspectRatio, $minAspectRatio));
                }
                if ($maxAspectRatio !== null && $this->_forceTargetAspectRatio > $maxAspectRatio) {
                    throw new \InvalidArgumentException(sprintf('Custom target aspect ratio (%.5f) must be lesser than or equal to the maximum aspect ratio (%.5f).',
                                                                $this->_forceTargetAspectRatio, $maxAspectRatio));
                }
            }
        }
        $this->_minAspectRatio = $minAspectRatio;
        $this->_maxAspectRatio = $maxAspectRatio;

        // Allow the aspect ratio of the final, new canvas to deviate slightly from the min/max range?
        $this->_allowNewAspectDeviation = $allowNewAspectDeviation;

        // Background color.
        if ($bgColor !== null && (!is_array($bgColor) || count($bgColor) !== 3 || !isset($bgColor[0]) || !isset($bgColor[1]) || !isset($bgColor[2]))) {
            throw new \InvalidArgumentException('The background color must be a 3-element array [R, G, B].');
        } elseif ($bgColor === null) {
            $bgColor = [255, 255, 255]; // White.
        }
        $this->_bgColor = $bgColor;

        //Blurred border
        $this->_blurredBorder = $blurredBorder;

        // Media operation.
        if ($operation !== self::CROP && $operation !== self::EXPAND) {
            throw new \InvalidArgumentException('The operation must be one of the class constants CROP or EXPAND.');
        }
        $this->_operation = $operation;

        // Temporary directory path.
        if ($tmpPath === null) {
            $tmpPath = self::$defaultTmpPath !== null
                       ? self::$defaultTmpPath
                       : sys_get_temp_dir();
        }
        if (!is_dir($tmpPath) || !is_writable($tmpPath)) {
            throw new \InvalidArgumentException(sprintf('Directory %s does not exist or is not writable.', $tmpPath));
        }
        $this->_tmpPath = realpath($tmpPath);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->deleteFile();
    }

    /**
     * Removes the output file if it exists and differs from input file.
     *
     * This function is safe and won't delete the original input file.
     *
     * Is automatically called when the class instance is destroyed by PHP.
     * But you can manually call it ahead of time if you want to force cleanup.
     *
     * Note that getFile() will still work afterwards, but will have to process
     * the media again to a new temp file if the input file required processing.
     *
     * @return bool
     */
    public function deleteFile()
    {
        // Only delete if outputfile exists and isn't the same as input file.
        if ($this->_outputFile !== null && $this->_outputFile !== $this->_inputFile && is_file($this->_outputFile)) {
            $result = @unlink($this->_outputFile);
            $this->_outputFile = null; // Reset so getFile() will work again.
            return $result;
        }

        return true;
    }

    /**
     * Gets the path to a media file matching the requirements.
     *
     * The automatic processing is performed the first time that this function
     * is called. Which means that no CPU time is wasted if you never call this
     * function at all.
     *
     * Due to the processing, the first call to this function may take a moment.
     *
     * If the input file already fits all of the specifications, we simply
     * return the input path instead, without any need to re-process it.
     *
     * @throws \Exception
     * @throws \RuntimeException
     *
     * @return string The path to the media file.
     *
     * @see InstagramMedia::_shouldProcess() The criteria that determines processing.
     */
    public function getFile()
    {
        if ($this->_outputFile === null) {
            $this->_outputFile = $this->_shouldProcess() ? $this->_process() : $this->_inputFile;
        }

        return $this->_outputFile;
    }

    /**
     * Checks whether we should process the input file.
     *
     * @return bool
     */
    protected function _shouldProcess()
    {
        $inputAspectRatio = $this->_details->getAspectRatio();

        // Process if aspect ratio < minimum allowed.
        if ($this->_minAspectRatio !== null && $inputAspectRatio < $this->_minAspectRatio) {
            return true;
        }

        // Process if aspect ratio > maximum allowed.
        if ($this->_maxAspectRatio !== null && $inputAspectRatio > $this->_maxAspectRatio) {
            return true;
        }

        // Process if USER provided the custom aspect ratio target and input deviates too much.
        if ($this->_hasUserForceTargetAspectRatio) {
            if ($this->_forceTargetAspectRatio == 1.0) {
                // User wants a SQUARE canvas, which can ALWAYS be achieved (by
                // making both sides equal). Process input if not EXACTLY square.
                // WARNING: Comparison here and above MUST use `!=` (NOT strict
                // `!==`) to support both int(1) and float(1.0) values!
                if ($inputAspectRatio != 1.0) {
                    return true;
                }
            } else {
                // User wants a non-square canvas, which is almost always
                // IMPOSSIBLE to achieve perfectly. Only process if input
                // deviates too much from the desired target.
                $acceptableDeviation = 0.003; // Allow a very narrow range around the user's target.
                $acceptableMinAspectRatio = $this->_forceTargetAspectRatio - $acceptableDeviation;
                $acceptableMaxAspectRatio = $this->_forceTargetAspectRatio + $acceptableDeviation;
                if ($inputAspectRatio < $acceptableMinAspectRatio || $inputAspectRatio > $acceptableMaxAspectRatio) {
                    return true;
                }
            }
        }

        // Process if the media can't be uploaded to Instagram as is.
        // NOTE: Nobody is allowed to call `isMod2CanvasRequired()` here. That
        // isn't its purpose. Whether a final Mod2 canvas is required for actual
        // resizing has NOTHING to do with whether the input file is ok.
        try {
            $this->_details->validate($this->_constraints);

            return false;
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Whether this processor requires Mod2 width and height canvas dimensions.
     *
     * If this returns FALSE, the calculated `InstagramMedia` canvas passed to
     * this processor _may_ contain uneven width and/or height as the selected
     * output dimensions.
     *
     * Therefore, this function must return TRUE if (and ONLY IF) perfectly even
     * dimensions are necessary for this particular processor's output format.
     *
     * For example, JPEG images accept any dimensions and must therefore return
     * FALSE. But H264 videos require EVEN dimensions and must return TRUE.
     *
     * @return bool
     */
    abstract protected function _isMod2CanvasRequired();

    /**
     * Process the input file and create the new file.
     *
     * @throws \RuntimeException
     *
     * @return string The path to the new file.
     */
    protected function _process()
    {
        // Get the dimensions of the original input file.
        $inputCanvas = new Dimensions($this->_details->getWidth(), $this->_details->getHeight());

        // Create an output canvas with the desired dimensions.
        // WARNING: This creates a LEGAL canvas which MUST be followed EXACTLY.
        $canvasInfo = $this->_calculateNewCanvas( // Throws.
            $this->_operation,
            $inputCanvas->getWidth(),
            $inputCanvas->getHeight(),
            $this->_isMod2CanvasRequired(),
            $this->_details->getMinAllowedWidth(),
            $this->_details->getMaxAllowedWidth(),
            $this->_minAspectRatio,
            $this->_maxAspectRatio,
            $this->_forceTargetAspectRatio,
            $this->_allowNewAspectDeviation
        );
        $outputCanvas = $canvasInfo['canvas'];

        // Determine the media operation's resampling parameters and perform it.
        // NOTE: This section is EXCESSIVELY commented to explain each step. The
        // algorithm is pretty easy after you understand it. But without the
        // detailed comments, future contributors may not understand any of it!
        // "We'd rather have a WaLL oF TeXt for future reference, than bugs due
        // to future misunderstandings!" - SteveJobzniak ;-)
        if ($this->_operation === self::CROP) {
            // Determine the IDEAL canvas dimensions as if Mod2 adjustments were
            // not applied. That's NECESSARY for calculating an ACCURATE scale-
            // change compared to the input, so that we can calculate how much
            // the canvas has rescaled. WARNING: These are 1-dimensional scales,
            // and only ONE value (the uncropped side) is valid for comparison.
            $idealCanvas = new Dimensions($outputCanvas->getWidth() - $canvasInfo['mod2WidthDiff'],
                                          $outputCanvas->getHeight() - $canvasInfo['mod2HeightDiff']);
            $idealWidthScale = (float) ($idealCanvas->getWidth() / $inputCanvas->getWidth());
            $idealHeightScale = (float) ($idealCanvas->getHeight() / $inputCanvas->getHeight());
            $this->_debugDimensions(
                $inputCanvas->getWidth(), $inputCanvas->getHeight(),
                'CROP: Analyzing Original Input Canvas Size'
            );
            $this->_debugDimensions(
                $idealCanvas->getWidth(), $idealCanvas->getHeight(),
                'CROP: Analyzing Ideally Cropped (Non-Mod2-adjusted) Output Canvas Size'
            );
            $this->_debugText(
                'CROP: Scale of Ideally Cropped Canvas vs Input Canvas',
                'width=%.8f, height=%.8f',
                $idealWidthScale, $idealHeightScale
            );

            // Now determine HOW the IDEAL canvas has been cropped compared to
            // the INPUT canvas. But we can't just compare dimensions, since our
            // algorithms may have cropped and THEN scaled UP the dimensions to
            // legal values far above the input values, or scaled them DOWN and
            // then Mod2-cropped at the new scale, etc. There are so many
            // possibilities. That's also why we couldn't "just keep track of
            // amount of pixels cropped during main algorithm". We MUST figure
            // it out ourselves accurately HERE. We can't do it at any earlier
            // stage, since cumulative rounding errors from width/height
            // readjustments could drift us away from the target aspect ratio
            // and could prevent pixel-perfect results UNLESS we calc it HERE.
            //
            // There's IS a great way to figure out the cropping. When the WIDTH
            // of a canvas is reduced (making it more "portraity"), its aspect
            // ratio number decreases. When the HEIGHT of a canvas is reduced
            // (making it more "landscapey"), its aspect ratio number increases.
            //
            // And our canvas cropping algorithm only crops in ONE DIRECTION
            // (width or height), so we only need to detect the aspect ratio
            // change of the IDEAL (non-Mod2-adjusted) canvas, to know what
            // happened. However, note that this CAN also trigger if the input
            // had to be up/downscaled (to an imperfect final aspect), but that
            // doesn't matter since this algorithm will STILL figure out the
            // proper scale and croppings to use for the canvas. Because uneven,
            // aspect-affecting scaling basically IS cropping the INPUT canvas!
            if ($idealCanvas->getAspectRatio() === $inputCanvas->getAspectRatio()) {
                // No sides have been cropped. So both width and height scales
                // WILL be IDENTICAL, since NOTHING else would be able to create
                // an identical aspect ratio again (otherwise the aspect ratio
                // would have been warped (not equal)). So just pick either one.
                // NOTE: Identical (uncropped ratio) DOESN'T mean that scale is
                // going to be 1.0. It MAY be. Or the canvas MAY have been
                // evenly expanded or evenly shrunk in both dimensions.
                $hasCropped = 'nothing';
                $overallRescale = $idealWidthScale; // $idealHeightScale IS identical.
            } elseif ($idealCanvas->getAspectRatio() < $inputCanvas->getAspectRatio()) {
                // The horizontal width has been cropped. Grab the height's
                // scale, since that side is "unaffected" by the main cropping
                // and should therefore have a scale of 1. Although it may have
                // had up/down-scaling. In that case, the height scale will
                // represent the amount of overall rescale change.
                $hasCropped = 'width';
                $overallRescale = $idealHeightScale;
            } else { // Output aspect is > input.
                // The vertical height has been cropped. Just like above, the
                // "unaffected" side is what we'll use as our scale reference.
                $hasCropped = 'height';
                $overallRescale = $idealWidthScale;
            }
            $this->_debugText(
                'CROP: Detecting Cropped Direction',
                'cropped=%s, overallRescale=%.8f',
                $hasCropped, $overallRescale
            );

            // Alright, now calculate the dimensions of the "IDEALLY CROPPED
            // INPUT canvas", at INPUT canvas scale. These are the scenarios:
            //
            // - "hasCropped: nothing, scale is 1.0" = Nothing was cropped, and
            //   nothing was scaled. Treat as "use whole INPUT canvas". This is
            //   pixel-perfect.
            //
            // - "hasCropped: nothing, scale NOT 1.0" = Nothing was cropped, but
            //   the whole canvas was up/down-scaled. We don't have to care at
            //   all about that scaling and should treat it as "use whole INPUT
            //   canvas" for crop calculation purposes. The cropped result will
            //   later be scaled/stretched to the canvas size (up or down).
            //
            // - "hasCropped: width/height, scale is 1.0" = A single side was
            //   cropped, and nothing was scaled. Treat as "use IDEALLY CROPPED
            //   canvas". This is pixel-perfect.
            //
            // - "hasCropped: width/height, scale NOT 1.0" = A single side was
            //   cropped, and then the whole canvas was up/down-scaled. Treat as
            //   "use scale-fixed version of IDEALLY CROPPED canvas". The
            //   cropped result will later be scaled/stretched to the canvas
            //   size (up or down).
            //
            // There's an easy way to handle ALL of those scenarios: Just
            // translate the IDEALLY CROPPED canvas back into INPUT-SCALED
            // dimensions. Then we'll get a pixel-perfect "input crop" whenever
            // scale is 1.0, since a scale of 1.0 gives the same result back.
            // And we'll get a properly re-scaled result in all other cases.
            //
            // NOTE: This result CAN deviate from what was "actually cropped"
            // during the main algorithm. That is TOTALLY INTENTIONAL AND IS THE
            // INTENDED, PERFECT BEHAVIOR! Do NOT change this code! By always
            // re-calculating here, we'll actually FIX rounding errors caused by
            // the main algorithm's multiple steps, and will create better
            // looking rescaling, and pixel-perfect unscaled croppings and
            // pixel-perfect unscaled Mod2 adjustments!

            // First calculate the overall IDEAL cropping applied to the INPUT
            // canvas. If scale is 1.0 it will be used as-is (pixel-perfect).
            // NOTE: We tell it to use round() so that the rescaled pixels are
            // as close to the perfect aspect ratio as possible.
            $croppedInputCanvas = $idealCanvas->withRescaling(1 / $overallRescale, 'round');
            $this->_debugDimensions(
                $croppedInputCanvas->getWidth(), $croppedInputCanvas->getHeight(),
                'CROP: Rescaled Ideally Cropped Canvas to Input Dimension Space'
            );

            // Now re-scale the Mod2 adjustments to the INPUT canvas coordinate
            // space too. If scale is 1.0 they'll be used as-is (pixel-perfect).
            // If the scale is up/down, they'll be rounded to the next whole
            // number. The rounding is INTENTIONAL, because if scaling was used
            // for the IDEAL canvas then it DOESN'T MATTER how many exact pixels
            // we crop, but round() gives us the BEST APPROXIMATION!
            $rescaledMod2WidthDiff = (int) round($canvasInfo['mod2WidthDiff'] * (1 / $overallRescale));
            $rescaledMod2HeightDiff = (int) round($canvasInfo['mod2HeightDiff'] * (1 / $overallRescale));
            $this->_debugText(
                'CROP: Rescaled Mod2 Adjustments to Input Dimension Space',
                'width=%s, height=%s, widthRescaled=%s, heightRescaled=%s',
                $canvasInfo['mod2WidthDiff'], $canvasInfo['mod2HeightDiff'],
                $rescaledMod2WidthDiff, $rescaledMod2HeightDiff
            );

            // Apply the Mod2 adjustments to the input cropping that we'll
            // perform. This ensures that ALL of the Mod2 croppings (in ANY
            // dimension) will always be pixel-perfect when we're at scale 1.0!
            $croppedInputCanvas = new Dimensions($croppedInputCanvas->getWidth() + $rescaledMod2WidthDiff,
                                                 $croppedInputCanvas->getHeight() + $rescaledMod2HeightDiff);
            $this->_debugDimensions(
                $croppedInputCanvas->getWidth(), $croppedInputCanvas->getHeight(),
                'CROP: Applied Mod2 Adjustments to Final Cropped Input Canvas'
            );

            // The "CROPPED INPUT canvas" is in the same dimensions/coordinate
            // space as the "INPUT canvas". So ensure all dimensions are valid
            // (don't exceed INPUT) and create the final "CROPPED INPUT canvas".
            // NOTE: This is it... if the media is at scale 1.0, we now have a
            // pixel-perfect, cropped canvas with ALL of the cropping and Mod2
            // adjustments applied to it! And if we're at another scale, we have
            // a perfectly recalculated, cropped canvas which took into account
            // cropping, scaling and Mod2 adjustments. Advanced stuff! :-)
            $croppedInputCanvasWidth = $croppedInputCanvas->getWidth() <= $inputCanvas->getWidth()
                                     ? $croppedInputCanvas->getWidth() : $inputCanvas->getWidth();
            $croppedInputCanvasHeight = $croppedInputCanvas->getHeight() <= $inputCanvas->getHeight()
                                      ? $croppedInputCanvas->getHeight() : $inputCanvas->getHeight();
            $croppedInputCanvas = new Dimensions($croppedInputCanvasWidth, $croppedInputCanvasHeight);
            $this->_debugDimensions(
                $croppedInputCanvas->getWidth(), $croppedInputCanvas->getHeight(),
                'CROP: Clamped to Legal Input Max-Dimensions'
            );

            // Initialize the crop-shifting variables. They control the range of
            // X/Y coordinates we'll copy from ORIGINAL INPUT to OUTPUT canvas.
            // NOTE: This properly selects the entire INPUT media canvas area.
            $x1 = $y1 = 0;
            $x2 = $inputCanvas->getWidth();
            $y2 = $inputCanvas->getHeight();
            $this->_debugText(
                'CROP: Initializing X/Y Variables to Full Input Canvas Size',
                'x1=%s, x2=%s, y1=%s, y2=%s',
                $x1, $x2, $y1, $y2
            );

            // Calculate the width and height diffs between the original INPUT
            // canvas and the new CROPPED INPUT canvas. Negative values mean the
            // output is smaller (which we'll handle by cropping), and larger
            // values would mean the output is larger (which we'll handle by
            // letting the OUTPUT canvas stretch the 100% uncropped original
            // pixels of the INPUT in that direction, to fill the whole canvas).
            // NOTE: Because of clamping of the CROPPED INPUT canvas above, this
            // will actually never be a positive ("scale up") number. It will
            // only be 0 or less. That's good, just be aware of it if editing!
            $widthDiff = $croppedInputCanvas->getWidth() - $inputCanvas->getWidth();
            $heightDiff = $croppedInputCanvas->getHeight() - $inputCanvas->getHeight();
            $this->_debugText(
                'CROP: Calculated Input Canvas Crop Amounts',
                'width=%s px, height=%s px',
                $widthDiff, $heightDiff
            );

            // After ALL of that work... we finally know how to crop the input
            // canvas! Alright... handle cropping of the INPUT width and height!
            // NOTE: The main canvas-creation algorithm only crops a single
            // dimension (width or height), but its Mod2 adjustments may have
            // caused BOTH to be cropped, which is why we MUST process both.
            if ($widthDiff < 0) {
                // Horizontal cropping. Focus on the center by default.
                $horCropFocus = $this->_horCropFocus !== null ? $this->_horCropFocus : 0;
                $this->_debugText('CROP: Horizontal Crop Focus', 'focus=%s', $horCropFocus);

                // Invert the focus if this is horizontally flipped media.
                if ($this->_details->isHorizontallyFlipped()) {
                    $horCropFocus = -$horCropFocus;
                    $this->_debugText(
                        'CROP: Media is HorFlipped, Flipping Horizontal Crop Focus',
                        'focus=%s',
                        $horCropFocus
                    );
                }

                // Calculate amount of pixels to crop and shift them as-focused.
                // NOTE: Always use floor() to make uneven amounts lean at left.
                $absWidthDiff = abs($widthDiff);
                $x1 = (int) floor($absWidthDiff * (50 + $horCropFocus) / 100);
                $x2 = $x2 - ($absWidthDiff - $x1);
                $this->_debugText('CROP: Calculated New X Offsets', 'x1=%s, x2=%s', $x1, $x2);
            }
            if ($heightDiff < 0) {
                // Vertical cropping. Focus on top by default (to keep faces).
                $verCropFocus = $this->_verCropFocus !== null ? $this->_verCropFocus : -50;
                $this->_debugText('CROP: Vertical Crop Focus', 'focus=%s', $verCropFocus);

                // Invert the focus if this is vertically flipped media.
                if ($this->_details->isVerticallyFlipped()) {
                    $verCropFocus = -$verCropFocus;
                    $this->_debugText(
                        'CROP: Media is VerFlipped, Flipping Vertical Crop Focus',
                        'focus=%s',
                        $verCropFocus
                    );
                }

                // Calculate amount of pixels to crop and shift them as-focused.
                // NOTE: Always use floor() to make uneven amounts lean at top.
                $absHeightDiff = abs($heightDiff);
                $y1 = (int) floor($absHeightDiff * (50 + $verCropFocus) / 100);
                $y2 = $y2 - ($absHeightDiff - $y1);
                $this->_debugText('CROP: Calculated New Y Offsets', 'y1=%s, y2=%s', $y1, $y2);
            }

            // Create a source rectangle which starts at the start-offsets
            // (x1/y1) and lasts until the width and height of the desired area.
            $srcRect = new Rectangle($x1, $y1, $x2 - $x1, $y2 - $y1);
            $this->_debugText(
                'CROP_SRC: Input Canvas Source Rectangle',
                'x1=%s, x2=%s, y1=%s, y2=%s, width=%s, height=%s, aspect=%.8f',
                $srcRect->getX1(), $srcRect->getX2(), $srcRect->getY1(), $srcRect->getY2(),
                $srcRect->getWidth(), $srcRect->getHeight(), $srcRect->getAspectRatio()
            );

            // Create a destination rectangle which completely fills the entire
            // output canvas from edge to edge. This ensures that any undersized
            // or oversized input will be stretched properly in all directions.
            //
            // NOTE: Everything about our cropping/canvas algorithms is
            // optimized so that stretching won't happen unless the media is so
            // tiny that it's below the minimum width or so wide that it must be
            // shrunk. Everything else WILL use sharp 1:1 pixels and pure
            // cropping instead of stretching/shrinking. And when stretch/shrink
            // is used, the aspect ratio is always perfectly maintained!
            $dstRect = new Rectangle(0, 0, $outputCanvas->getWidth(), $outputCanvas->getHeight());
            $this->_debugText(
                'CROP_DST: Output Canvas Destination Rectangle',
                'x1=%s, x2=%s, y1=%s, y2=%s, width=%s, height=%s, aspect=%.8f',
                $dstRect->getX1(), $dstRect->getX2(), $dstRect->getY1(), $dstRect->getY2(),
                $dstRect->getWidth(), $dstRect->getHeight(), $dstRect->getAspectRatio()
            );
        } elseif ($this->_operation === self::EXPAND) {
            // We'll copy the entire original input media onto the new canvas.
            // Always copy from the absolute top left of the original media.
            $srcRect = new Rectangle(0, 0, $inputCanvas->getWidth(), $inputCanvas->getHeight());
            $this->_debugText(
                'EXPAND_SRC: Input Canvas Source Rectangle',
                'x1=%s, x2=%s, y1=%s, y2=%s, width=%s, height=%s, aspect=%.8f',
                $srcRect->getX1(), $srcRect->getX2(), $srcRect->getY1(), $srcRect->getY2(),
                $srcRect->getWidth(), $srcRect->getHeight(), $srcRect->getAspectRatio()
            );

            // Determine the target dimensions to fit it on the new canvas,
            // because the input media's dimensions may have been too large.
            // This will not scale anything (uses scale=1) if the input fits.
            $outputWidthScale = (float) ($outputCanvas->getWidth() / $inputCanvas->getWidth());
            $outputHeightScale = (float) ($outputCanvas->getHeight() / $inputCanvas->getHeight());
            $scale = min($outputWidthScale, $outputHeightScale);
            $this->_debugText(
                'EXPAND: Calculating Scale to Fit Input on Output Canvas',
                'scale=%.8f',
                $scale
            );

            // Calculate the scaled destination rectangle. Note that X/Y remain.
            // NOTE: We tell it to use ceil(), which guarantees that it'll
            // never scale a side badly and leave a 1px gap between the media
            // and canvas sides. Also note that ceil will never produce bad
            // values, since PHP allows the dst_w/dst_h to exceed beyond canvas!
            $dstRect = $srcRect->withRescaling($scale, 'ceil');
            $this->_debugDimensions(
                $dstRect->getWidth(), $dstRect->getHeight(),
                'EXPAND: Rescaled Input to Output Dimension Space'
            );

            // Now calculate the centered destination offset on the canvas.
            // NOTE: We use floor() to ensure that the result gets left-aligned
            // perfectly, and prefers to lean towards towards the top as well.
            $dst_x = (int) floor(($outputCanvas->getWidth() - $dstRect->getWidth()) / 2);
            $dst_y = (int) floor(($outputCanvas->getHeight() - $dstRect->getHeight()) / 2);
            $this->_debugText(
                'EXPAND: Calculating Centered Destination on Output Canvas',
                'dst_x=%s, dst_y=%s',
                $dst_x, $dst_y
            );

            // Build the final destination rectangle for the expanded canvas!
            $dstRect = new Rectangle($dst_x, $dst_y, $dstRect->getWidth(), $dstRect->getHeight());
            $this->_debugText(
                'EXPAND_DST: Output Canvas Destination Rectangle',
                'x1=%s, x2=%s, y1=%s, y2=%s, width=%s, height=%s, aspect=%.8f',
                $dstRect->getX1(), $dstRect->getX2(), $dstRect->getY1(), $dstRect->getY2(),
                $dstRect->getWidth(), $dstRect->getHeight(), $dstRect->getAspectRatio()
            );
        } else {
            throw new \RuntimeException(sprintf('Unsupported operation: %s.', $this->_operation));
        }

        return $this->_createOutputFile($srcRect, $dstRect, $outputCanvas);
    }

    /**
     * Create the new media file.
     *
     * @param Rectangle  $srcRect Rectangle to copy from the input.
     * @param Rectangle  $dstRect Destination place and scale of copied pixels.
     * @param Dimensions $canvas  The size of the destination canvas.
     *
     * @return string The path to the output file.
     */
    abstract protected function _createOutputFile(
        Rectangle $srcRect,
        Rectangle $dstRect,
        Dimensions $canvas);

    /**
     * Calculate a new canvas based on input size and requested modifications.
     *
     * The final canvas will be the same size as the input if everything was
     * already okay and within the limits. Otherwise it will be a new canvas
     * representing the _exact_, best-possible size to convert input media to.
     *
     * It is up to the caller to perfectly follow these orders, since deviating
     * by even a SINGLE PIXEL can create illegal media aspect ratios.
     *
     * Also note that the resulting canvas can be LARGER than the input in
     * several cases, such as in EXPAND-mode (obviously), or when the input
     * isn't wide enough to be legal (and must be scaled up), and whenever Mod2
     * is requested. In the latter case, the algorithm may have to add a few
     * pixels to the height to make it valid in a few rare cases. The caller
     * must be aware of such "enlarged" canvases and should handle them by
     * stretching the input if necessary.
     *
     * @param int        $operation
     * @param int        $inputWidth
     * @param int        $inputHeight
     * @param bool       $isMod2CanvasRequired
     * @param int        $minWidth
     * @param int        $maxWidth
     * @param float|null $minAspectRatio
     * @param float|null $maxAspectRatio
     * @param float|null $forceTargetAspectRatio  Optional forced aspect ratio
     *                                            target (ALWAYS applied,
     *                                            except if input is already
     *                                            EXACTLY this ratio).
     * @param bool       $allowNewAspectDeviation See constructor arg docs.
     *
     * @throws \RuntimeException If requested canvas couldn't be achieved, most
     *                           commonly if you have chosen way too narrow
     *                           aspect ratio ranges that cannot be perfectly
     *                           reached by your input media, and you AREN'T
     *                           running with `$allowNewAspectDeviation`.
     *
     * @return array An array with `canvas` (`Dimensions`), `mod2WidthDiff` and
     *               `mod2HeightDiff`. The latter are integers representing how
     *               many pixels were cropped (-) or added (+) by the Mod2 step
     *               compared to the ideal canvas.
     */
    protected function _calculateNewCanvas(
        $operation,
        $inputWidth,
        $inputHeight,
        $isMod2CanvasRequired,
        $minWidth = 1,
        $maxWidth = 99999,
        $minAspectRatio = null,
        $maxAspectRatio = null,
        $forceTargetAspectRatio = null,
        $allowNewAspectDeviation = false)
    {
        /*
         * WARNING TO POTENTIAL CONTRIBUTORS:
         *
         * THIS right here is the MOST COMPLEX algorithm in the whole project.
         * Everything is finely tuned to create 100% accurate, pixel-perfect
         * resizes. A SINGLE PIXEL ERROR in your calculations WILL lead to it
         * sometimes outputting illegally formatted files that will be rejected
         * by Instagram. We know this, because we have SEEN IT HAPPEN while we
         * tweaked and tweaked and tweaked to balance everything perfectly!
         *
         * Unfortunately, this file also seems to attract a lot of beginners.
         * Maybe because a "media processor" seems "fun and easy". But that
         * would be an incorrect guess. It's the most serious algorithm in the
         * whole project. If you break it, *YOU* break people's uploads.
         *
         * We have had many random, new contributors just jumping in and adding
         * zero-effort code everywhere in here, and breaking the whole balance,
         * and then opening pull requests. We have rejected EVERY single one of
         * those pull requests because they were totally unusable and unsafe.
         *
         * We will not accept such pull requests. Ever.
         *
         * This warning is here to save your time, and ours.
         *
         * If you are interested in helping out with the media algorithms, then
         * that's GREAT! But in that case we require that you fully read through
         * the algorithms below and all of its comments about 50 times over a
         * 3-4 day period - until you understand every single step perfectly.
         * The comments will help make it clearer the more you read...
         *
         *                                               ...and make an effort.
         *
         * Then you are ready... and welcome to the team. :-)
         *
         * Thank you.
         */

        if ($forceTargetAspectRatio !== null) {
            $this->_debugText('SPECIAL_PARAMETERS: Forced Target Aspect Ratio', 'forceTargetAspectRatio=%.5f', $forceTargetAspectRatio);
        }

        // Initialize target canvas to original input dimensions & aspect ratio.
        // NOTE: MUST `float`-cast to FORCE float even when dividing EQUAL ints.
        $targetWidth = (int) $inputWidth;
        $targetHeight = (int) $inputHeight;
        $targetAspectRatio = (float) ($inputWidth / $inputHeight);
        $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS_INPUT: Input Canvas Size');

        // Check aspect ratio and crop/expand the canvas to fit aspect if needed.
        if (
            ($minAspectRatio !== null && $targetAspectRatio < $minAspectRatio)
            || ($forceTargetAspectRatio !== null && $targetAspectRatio < $forceTargetAspectRatio)
        ) {
            // Determine target ratio; uses forced aspect ratio if set,
            // otherwise we target the MINIMUM allowed ratio (since we're < it)).
            $targetAspectRatio = $forceTargetAspectRatio !== null ? $forceTargetAspectRatio : $minAspectRatio;

            if ($operation === self::CROP) {
                // We need to limit the height, so floor is used intentionally to
                // AVOID rounding height upwards to a still-too-low aspect ratio.
                $targetHeight = (int) floor($targetWidth / $targetAspectRatio);
                $this->_debugDimensions($targetWidth, $targetHeight, sprintf('CANVAS_CROPPED: %s', $forceTargetAspectRatio === null ? 'Aspect Was < MIN' : 'Applying Forced Aspect for INPUT < TARGET'));
            } elseif ($operation === self::EXPAND) {
                // We need to expand the width with left/right borders. We use
                // ceil to guarantee that the final media is wide enough to be
                // above the minimum allowed aspect ratio.
                $targetWidth = (int) ceil($targetHeight * $targetAspectRatio);
                $this->_debugDimensions($targetWidth, $targetHeight, sprintf('CANVAS_EXPANDED: %s', $forceTargetAspectRatio === null ? 'Aspect Was < MIN' : 'Applying Forced Aspect for INPUT < TARGET'));
            }
        } elseif (
            ($maxAspectRatio !== null && $targetAspectRatio > $maxAspectRatio)
            || ($forceTargetAspectRatio !== null && $targetAspectRatio > $forceTargetAspectRatio)
        ) {
            // Determine target ratio; uses forced aspect ratio if set,
            // otherwise we target the MAXIMUM allowed ratio (since we're > it)).
            $targetAspectRatio = $forceTargetAspectRatio !== null ? $forceTargetAspectRatio : $maxAspectRatio;

            if ($operation === self::CROP) {
                // We need to limit the width. We use floor to guarantee cutting
                // enough pixels, since our width exceeds the maximum allowed ratio.
                $targetWidth = (int) floor($targetHeight * $targetAspectRatio);
                $this->_debugDimensions($targetWidth, $targetHeight, sprintf('CANVAS_CROPPED: %s', $forceTargetAspectRatio === null ? 'Aspect Was > MAX' : 'Applying Forced Aspect for INPUT > TARGET'));
            } elseif ($operation === self::EXPAND) {
                // We need to expand the height with top/bottom borders. We use
                // ceil to guarantee that the final media is tall enough to be
                // below the maximum allowed aspect ratio.
                $targetHeight = (int) ceil($targetWidth / $targetAspectRatio);
                $this->_debugDimensions($targetWidth, $targetHeight, sprintf('CANVAS_EXPANDED: %s', $forceTargetAspectRatio === null ? 'Aspect Was > MAX' : 'Applying Forced Aspect for INPUT > TARGET'));
            }
        } else {
            $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS: Aspect Ratio Already Legal');
        }

        // Determine whether the final target ratio is closest to either the
        // legal MINIMUM or the legal MAXIMUM aspect ratio limits.
        // NOTE: The target ratio will actually still be set to the original
        // input media's ratio in case of no aspect ratio adjustments above.
        // NOTE: If min and/or max ratios were not provided, we default min to
        // `0` and max to `9999999` to ensure that we properly detect the "least
        // distance" direction even when only one (or neither) of the two "range
        // limit values" were provided.
        $minAspectDistance = abs(($minAspectRatio !== null
                                  ? $minAspectRatio : 0) - $targetAspectRatio);
        $maxAspectDistance = abs(($maxAspectRatio !== null
                                  ? $maxAspectRatio : 9999999) - $targetAspectRatio);
        $isClosestToMinAspect = ($minAspectDistance <= $maxAspectDistance);

        // We MUST now set up the correct height re-calculation behavior for the
        // later algorithm steps. This is used whenever our canvas needs to be
        // re-scaled by any other code below. If our chosen, final target ratio
        // is closest to the minimum allowed legal ratio, we'll always use
        // floor() on the height to ensure that the height value becomes as low
        // as possible (since having LESS height compared to width is what
        // causes the aspect ratio value to grow), to ensure that the final
        // result's ratio (after any additional adjustments) will ALWAYS be
        // ABOVE the minimum legal ratio (minAspectRatio). Otherwise we'll
        // instead use ceil() on the height (since having more height causes the
        // aspect ratio value to shrink), to ensure that the result is always
        // BELOW the maximum ratio (maxAspectRatio).
        $useFloorHeightRecalc = $isClosestToMinAspect;

        // Verify square target ratios by ensuring canvas is now a square.
        // NOTE: This is just a sanity check against wrong code above. It will
        // never execute, since all code above took care of making both
        // dimensions identical already (if they differed in any way, they had a
        // non-1 ratio and invoked the aspect ratio cropping/expansion code). It
        // then made identical thanks to the fact that X / 1 = X, and X * 1 = X.
        // NOTE: It's worth noting that our squares are always the size of the
        // shortest side when cropping or the longest side when expanding.
        // WARNING: Comparison MUST use `==` (NOT strict `===`) to support both
        // int(1) and float(1.0) values!
        if ($targetAspectRatio == 1.0 && $targetWidth !== $targetHeight) { // Ratio 1 = Square.
            $targetWidth = $targetHeight = $operation === self::CROP
                         ? min($targetWidth, $targetHeight)
                         : max($targetWidth, $targetHeight);
            $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS_SQUARIFY: Fixed Badly Generated Square');
        }

        // Lastly, enforce minimum and maximum width limits on our final canvas.
        // NOTE: Instagram only enforces width & aspect ratio, which in turn
        // auto-limits height (since we can only use legal height ratios).
        // NOTE: Yet again, if the target ratio is 1 (square), we'll get
        // identical width & height, so NO NEED to MANUALLY "fix square" here.
        if ($targetWidth > $maxWidth) {
            $targetWidth = $maxWidth;
            $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS_WIDTH: Width Was > MAX');
            $targetHeight = $this->_accurateHeightRecalc($useFloorHeightRecalc, $targetAspectRatio, $targetWidth);
            $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS_WIDTH: Height Recalc From Width & Aspect');
        } elseif ($targetWidth < $minWidth) {
            $targetWidth = $minWidth;
            $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS_WIDTH: Width Was < MIN');
            $targetHeight = $this->_accurateHeightRecalc($useFloorHeightRecalc, $targetAspectRatio, $targetWidth);
            $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS_WIDTH: Height Recalc From Width & Aspect');
        }

        // All of the main canvas algorithms are now finished, and we are now
        // able to check Mod2 compatibility and accurately readjust if needed.
        $mod2WidthDiff = $mod2HeightDiff = 0;
        if ($isMod2CanvasRequired
            && (!$this->_isNumberMod2($targetWidth) || !$this->_isNumberMod2($targetHeight))
        ) {
            // Calculate the Mod2-adjusted final canvas size.
            $mod2Canvas = $this->_calculateAdjustedMod2Canvas(
                $inputWidth,
                $inputHeight,
                $useFloorHeightRecalc,
                $targetWidth,
                $targetHeight,
                $targetAspectRatio,
                $minWidth,
                $maxWidth,
                $minAspectRatio,
                $maxAspectRatio,
                $allowNewAspectDeviation
            );

            // Determine the pixel difference before and after processing.
            $mod2WidthDiff = $mod2Canvas->getWidth() - $targetWidth;
            $mod2HeightDiff = $mod2Canvas->getHeight() - $targetHeight;
            $this->_debugText('CANVAS: Mod2 Difference Stats', 'width=%s, height=%s', $mod2WidthDiff, $mod2HeightDiff);

            // Update the final canvas to the Mod2-adjusted canvas size.
            // NOTE: If code above failed, the new values are invalid. But so
            // could our original values have been. We check that further down.
            $targetWidth = $mod2Canvas->getWidth();
            $targetHeight = $mod2Canvas->getHeight();
            $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS: Updated From Mod2 Result');
        }

        // Create the new canvas Dimensions object.
        $canvas = new Dimensions($targetWidth, $targetHeight);
        $this->_debugDimensions($targetWidth, $targetHeight, 'CANVAS_OUTPUT: Final Output Canvas Size');

        // We must now validate the canvas before returning it.
        // NOTE: Most of these are just strict sanity-checks to protect against
        // bad code contributions in the future. The canvas won't be able to
        // pass all of these checks unless the algorithm above remains perfect.
        $isIllegalRatio = (($minAspectRatio !== null && $canvas->getAspectRatio() < $minAspectRatio)
                           || ($maxAspectRatio !== null && $canvas->getAspectRatio() > $maxAspectRatio));
        if ($canvas->getWidth() < 1 || $canvas->getHeight() < 1) {
            throw new \RuntimeException(sprintf(
                'Canvas calculation failed. Target width (%s) or height (%s) less than one pixel.',
                $canvas->getWidth(), $canvas->getHeight()
            ));
        } elseif ($canvas->getWidth() < $minWidth) {
            throw new \RuntimeException(sprintf(
                'Canvas calculation failed. Target width (%s) less than minimum allowed (%s).',
                $canvas->getWidth(), $minWidth
            ));
        } elseif ($canvas->getWidth() > $maxWidth) {
            throw new \RuntimeException(sprintf(
                'Canvas calculation failed. Target width (%s) greater than maximum allowed (%s).',
                $canvas->getWidth(), $maxWidth
            ));
        } elseif ($isIllegalRatio) {
            if (!$allowNewAspectDeviation) {
                throw new \RuntimeException(sprintf(
                    'Canvas calculation failed. Unable to reach target aspect ratio range during output canvas generation. The range of allowed aspect ratios is too narrow (%.8f - %.8f). We achieved a ratio of %.8f.',
                    $minAspectRatio !== null ? $minAspectRatio : 0.0,
                    $maxAspectRatio !== null ? $maxAspectRatio : INF,
                    $canvas->getAspectRatio()
                ));
            } else {
                // The user wants us to allow "near-misses", so we proceed...
                $this->_debugDimensions($canvas->getWidth(), $canvas->getHeight(), 'CANVAS_FINAL: Allowing Deviating Aspect Ratio');
            }
        }

        return [
            'canvas'         => $canvas,
            'mod2WidthDiff'  => $mod2WidthDiff,
            'mod2HeightDiff' => $mod2HeightDiff,
        ];
    }

    /**
     * Calculates a new relative height using the target aspect ratio.
     *
     * Used internally by `_calculateNewCanvas()`.
     *
     * This algorithm aims at the highest-possible or lowest-possible resulting
     * aspect ratio based on what's needed. It uses either `floor()` or `ceil()`
     * depending on whether we need the resulting aspect ratio to be >= or <=
     * the target aspect ratio.
     *
     * The principle behind this is the fact that removing height (via floor)
     * will give us a higher aspect ratio. And adding height (via ceil) will
     * give us a lower aspect ratio.
     *
     * If the target aspect ratio is square (1), height becomes equal to width.
     *
     * @param bool  $useFloorHeightRecalc
     * @param float $targetAspectRatio
     * @param int   $targetWidth
     *
     * @return int
     */
    protected function _accurateHeightRecalc(
        $useFloorHeightRecalc,
        $targetAspectRatio,
        $targetWidth)
    {
        // Read the docs above to understand this CRITICALLY IMPORTANT code.
        $targetHeight = $useFloorHeightRecalc
                      ? (int) floor($targetWidth / $targetAspectRatio) // >=
                      : (int) ceil($targetWidth / $targetAspectRatio); // <=

        return $targetHeight;
    }

    /**
     * Adjusts dimensions to create a Mod2-compatible canvas.
     *
     * Used internally by `_calculateNewCanvas()`.
     *
     * The reason why this function also takes the original input width/height
     * is because it tries to maximize its usage of the available original pixel
     * surface area while correcting the dimensions. It uses the extra
     * information to know when it's safely able to grow the canvas beyond the
     * given target width/height parameter values.
     *
     * @param int        $inputWidth
     * @param int        $inputHeight
     * @param bool       $useFloorHeightRecalc
     * @param int        $targetWidth
     * @param int        $targetHeight
     * @param float      $targetAspectRatio
     * @param int        $minWidth
     * @param int        $maxWidth
     * @param float|null $minAspectRatio
     * @param float|null $maxAspectRatio
     * @param bool       $allowNewAspectDeviation See constructor arg docs.
     *
     * @throws \RuntimeException If requested canvas couldn't be achieved, most
     *                           commonly if you have chosen way too narrow
     *                           aspect ratio ranges that cannot be perfectly
     *                           reached by your input media, and you AREN'T
     *                           running with `$allowNewAspectDeviation`.
     *
     * @return Dimensions
     *
     * @see InstagramMedia::_calculateNewCanvas()
     */
    protected function _calculateAdjustedMod2Canvas(
        $inputWidth,
        $inputHeight,
        $useFloorHeightRecalc,
        $targetWidth,
        $targetHeight,
        $targetAspectRatio,
        $minWidth = 1,
        $maxWidth = 99999,
        $minAspectRatio = null,
        $maxAspectRatio = null,
        $allowNewAspectDeviation = false)
    {
        // Initialize to the calculated canvas size.
        $mod2Width = $targetWidth;
        $mod2Height = $targetHeight;
        $this->_debugDimensions($mod2Width, $mod2Height, 'MOD2_CANVAS: Current Canvas Size');

        // Determine if we're able to cut an extra pixel from the width if
        // necessary, or if cutting would take us below the minimum width.
        $canCutWidth = $mod2Width > $minWidth;

        // To begin, we must correct the width if it's uneven. We'll only do
        // this once, and then we'll leave the width at its new number. By
        // keeping it static, we don't risk going over its min/max width
        // limits. And by only varying one dimension (height) if multiple Mod2
        // offset adjustments are needed, then we'll properly get a steadily
        // increasing/decreasing aspect ratio (moving towards the target ratio).
        if (!$this->_isNumberMod2($mod2Width)) {
            // Always prefer cutting an extra pixel, rather than stretching
            // by +1. But use +1 if cutting would take us below minimum width.
            // NOTE: Another IMPORTANT reason to CUT width rather than extend
            // is because in narrow cases (canvas close to original input size),
            // the extra width proportionally increases total area (thus height
            // too), and gives us less of the original pixels on the height-axis
            // to play with when attempting to fix the height (and its ratio).
            $mod2Width += ($canCutWidth ? -1 : 1);
            $this->_debugDimensions($mod2Width, $mod2Height, 'MOD2_CANVAS: Width Mod2Fix');

            // Calculate the new relative height based on the new width.
            $mod2Height = $this->_accurateHeightRecalc($useFloorHeightRecalc, $targetAspectRatio, $mod2Width);
            $this->_debugDimensions($mod2Width, $mod2Height, 'MOD2_CANVAS: Height Recalc From Width & Aspect');
        }

        // Ensure that the calculated height is also Mod2, but totally ignore
        // the aspect ratio at this moment (we'll fix that later). Instead,
        // we'll use the same pattern we'd use for width above. That way, if
        // both width and height were uneven, they both get adjusted equally.
        if (!$this->_isNumberMod2($mod2Height)) {
            $mod2Height += ($canCutWidth ? -1 : 1);
            $this->_debugDimensions($mod2Width, $mod2Height, 'MOD2_CANVAS: Height Mod2Fix');
        }

        // We will now analyze multiple different height alternatives to find
        // which one gives us the best visual quality. This algorithm looks
        // for the best qualities (with the most pixel area) first. It first
        // tries the current height (offset 0, which is the closest to the
        // pre-Mod2 adjusted canvas), then +2 pixels (gives more pixel area if
        // this is possible), then -2 pixels (cuts but may be our only choice).
        // After that, it checks 4, -4, 6 and -6 as well.
        // NOTE: Every increased offset (+/-2, then +/-4, then +/- 6) USUALLY
        // (but not always) causes more and more deviation from the intended
        // cropping aspect ratio. So don't add any more steps after 6, since
        // NOTHING will be THAT far off! Six was chosen as a good balance.
        // NOTE: Every offset is checked for visual stretching and aspect ratio,
        // and then rated into one of 3 categories: "perfect" (legal aspect
        // ratio, no stretching), "stretch" (legal aspect ratio, but stretches),
        // or "bad" (illegal aspect ratio).
        $heightAlternatives = ['perfect' => [], 'stretch' => [], 'bad' => []];
        static $offsetPriorities = [0, 2, -2, 4, -4, 6, -6];
        foreach ($offsetPriorities as $offset) {
            // Calculate the new height and its resulting aspect ratio.
            // NOTE: MUST `float`-cast to FORCE float even when dividing EQUAL ints.
            $offsetMod2Height = $mod2Height + $offset;
            $offsetMod2AspectRatio = (float) ($mod2Width / $offsetMod2Height);

            // Check if the aspect ratio is legal.
            $isLegalRatio = (($minAspectRatio === null || $offsetMod2AspectRatio >= $minAspectRatio)
                             && ($maxAspectRatio === null || $offsetMod2AspectRatio <= $maxAspectRatio));

            // Detect whether the height would need stretching. Stretching is
            // defined as "not enough pixels in the input media to reach".
            // NOTE: If the input media has been upscaled (such as a 64x64 image
            // being turned into 320x320), then we will ALWAYS detect that media
            // as needing stretching. That's intentional and correct, because
            // such media will INDEED need stretching, so there's never going to
            // be a perfect rating for it (where aspect ratio is legal AND zero
            // stretching is needed to reach those dimensions).
            // NOTE: The max() gets rid of negative values (cropping).
            $stretchAmount = max(0, $offsetMod2Height - $inputHeight);

            // Calculate the deviation from the target aspect ratio. The larger
            // this number is, the further away from "the ideal canvas". The
            // "perfect" answers will always deviate by different amount, and
            // the most perfect one is the one with least deviation.
            $ratioDeviation = abs($offsetMod2AspectRatio - $targetAspectRatio);

            // Rate this height alternative and store it according to rating.
            $rating = ($isLegalRatio && !$stretchAmount ? 'perfect' : ($isLegalRatio ? 'stretch' : 'bad'));
            $heightAlternatives[$rating][] = [
                'offset'         => $offset,
                'height'         => $offsetMod2Height,
                'ratio'          => $offsetMod2AspectRatio,
                'isLegalRatio'   => $isLegalRatio,
                'stretchAmount'  => $stretchAmount,
                'ratioDeviation' => $ratioDeviation,
                'rating'         => $rating,
            ];
            $this->_debugDimensions($mod2Width, $offsetMod2Height, sprintf(
                'MOD2_CANVAS_CHECK: Testing Height Mod2Ratio (h%s%s = %s)',
                ($offset >= 0 ? '+' : ''), $offset, $rating)
            );
        }

        // Now pick the BEST height from our available choices (if any). We will
        // pick the LEGAL height that has the LEAST amount of deviation from the
        // ideal aspect ratio. In other words, the BEST-LOOKING aspect ratio!
        // NOTE: If we find no legal (perfect or stretch) choices, we'll pick
        // the most accurate (least deviation from ratio) of the bad choices.
        $bestHeight = null;
        foreach (['perfect', 'stretch', 'bad'] as $rating) {
            if (!empty($heightAlternatives[$rating])) {
                // Sort all alternatives by their amount of ratio deviation.
                usort($heightAlternatives[$rating], function ($a, $b) {
                    return ($a['ratioDeviation'] < $b['ratioDeviation'])
                        ? -1 : (($a['ratioDeviation'] > $b['ratioDeviation']) ? 1 : 0);
                });

                // Pick the 1st array element, which has the least deviation!
                $bestHeight = $heightAlternatives[$rating][0];
                break;
            }
        }

        // Process and apply the best-possible height we found.
        $mod2Height = $bestHeight['height'];
        $this->_debugDimensions($mod2Width, $mod2Height, sprintf(
            'MOD2_CANVAS: Selected Most Ideal Height Mod2Ratio (h%s%s = %s)',
            ($bestHeight['offset'] >= 0 ? '+' : ''), $bestHeight['offset'], $bestHeight['rating']
        ));

        // Decide what to do if there were no legal aspect ratios among our
        // calculated choices. This can happen if the user gave us an insanely
        // narrow range (such as "min/max ratio 1.6578" or whatever).
        if ($bestHeight['rating'] === 'bad') {
            if (!$allowNewAspectDeviation) {
                throw new \RuntimeException(sprintf(
                    'Canvas calculation failed. Unable to reach target aspect ratio range during Mod2 canvas conversion. The range of allowed aspect ratios is too narrow (%.8f - %.8f). We achieved a ratio of %.8f.',
                    $minAspectRatio !== null ? $minAspectRatio : 0.0,
                    $maxAspectRatio !== null ? $maxAspectRatio : INF,
                    (float) ($mod2Width / $mod2Height)
                ));
            } else {
                // They WANT us to allow "near-misses", so we'll KEEP our best
                // possible bad ratio here (the one that was closest to the
                // target). We didn't find any more ideal aspect ratio (since
                // all other attempts ALSO FAILED the aspect ratio ranges), so
                // we have NO idea if they'd prefer any others! ;-)
                $this->_debugDimensions($mod2Width, $mod2Height, sprintf(
                    'MOD2_CANVAS: Allowing Deviating Height Mod2Ratio (h%s%s = %s)',
                    ($bestHeight['offset'] >= 0 ? '+' : ''), $bestHeight['offset'], $bestHeight['rating']
                ));
            }
        }

        return new Dimensions($mod2Width, $mod2Height);
    }

    /**
     * Checks whether a number is Mod2.
     *
     * @param int|float $number
     *
     * @return bool
     */
    protected function _isNumberMod2(
        $number)
    {
        // NOTE: The modulo operator correctly returns ints even for float input such as 1.999.
        return $number % 2 === 0;
    }

    /**
     * Output debug text.
     *
     * @param string $stepDescription
     * @param string $formatMessage
     * @param mixed  $args,...
     */
    protected function _debugText(
        $stepDescription,
        $formatMessage,
        ...$args)
    {
        if (!$this->_debug) {
            return;
        }

        printf(
            "[\033[1;33m%s\033[0m] {$formatMessage}\n",
            $stepDescription,
            ...$args
        );
    }

    /**
     * Debug current calculation dimensions and their ratio.
     *
     * @param int|float   $width
     * @param int|float   $height
     * @param string|null $stepDescription
     */
    protected function _debugDimensions(
        $width,
        $height,
        $stepDescription = null)
    {
        if (!$this->_debug) {
            return;
        }

        printf(
            // NOTE: This uses 8 decimals for proper debugging, since small
            // rounding errors can make rejected ratios look valid.
            "[\033[1;33m%s\033[0m] w=%s h=%s (aspect %.8f)\n",
            $stepDescription !== null ? $stepDescription : 'DEBUG',
            $width, $height, (float) ($width / $height)
        );
    }
}
