<?php

namespace InstagramAPI;

use InstagramAPI\Media\Video\FFmpeg;
use InstagramAPI\Response\Model\Item;
use InstagramAPI\Response\Model\Location;

class Utils
{
    /**
     * Override for the default temp path used by various class functions.
     *
     * If this value is non-null, we'll use it. Otherwise we'll use the default
     * system tmp folder.
     *
     * TIP: If your default system temp folder isn't writable, it's NECESSARY
     * for you to set this value to another, writable path, like this:
     *
     * \InstagramAPI\Utils::$defaultTmpPath = '/home/example/foo/';
     */
    public static $defaultTmpPath = null;

    /**
     * Used for multipart boundary generation.
     *
     * @var string
     */
    const BOUNDARY_CHARS = '-_1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Length of generated multipart boundary.
     *
     * @var int
     */
    const BOUNDARY_LENGTH = 30;

    /**
     * Name of the detected ffmpeg executable.
     *
     * @var string|null
     *
     * @deprecated
     * @see FFmpeg::$defaultBinary
     */
    public static $ffmpegBin = null;

    /**
     * Name of the detected ffprobe executable, or FALSE if none found.
     *
     * @var string|bool|null
     */
    public static $ffprobeBin = null;

    /**
     * Last uploadId generated with microtime().
     *
     * @var string|null
     */
    protected static $_lastUploadId = null;

    /**
     * @param bool $useNano Whether to return result in usec instead of msec.
     *
     * @return string
     */
    public static function generateUploadId(
        $useNano = false)
    {
        $result = null;
        if (!$useNano) {
            while (true) {
                $result = number_format(round(microtime(true) * 1000), 0, '', '');
                if (self::$_lastUploadId !== null && $result === self::$_lastUploadId) {
                    // NOTE: Fast machines can process files too quick (< 0.001
                    // sec), which leads to identical upload IDs, which leads to
                    // "500 Oops, an error occurred" errors. So we sleep 0.001
                    // sec to guarantee different upload IDs per each call.
                    usleep(1000);
                } else { // OK!
                    self::$_lastUploadId = $result;
                    break;
                }
            }
        } else {
            // Emulate System.nanoTime().
            $result = number_format(microtime(true) - strtotime('Last Monday'), 6, '', '');
            // Append nanoseconds.
            $result .= str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        }

        return $result;
    }

    /**
     * Calculates Java hashCode() for a given string.
     *
     * WARNING: This method is not Unicode-aware, so use it only on ANSI strings.
     *
     * @param string $string
     *
     * @return int
     *
     * @see https://en.wikipedia.org/wiki/Java_hashCode()#The_java.lang.String_hash_function
     */
    public static function hashCode(
        $string)
    {
        $result = 0;
        for ($i = 0, $len = strlen($string); $i < $len; ++$i) {
            $result = (-$result + ($result << 5) + ord($string[$i])) & 0xFFFFFFFF;
        }
        if (PHP_INT_SIZE > 4) {
            if ($result > 0x7FFFFFFF) {
                $result -= 0x100000000;
            } elseif ($result < -0x80000000) {
                $result += 0x100000000;
            }
        }

        return $result;
    }

    /**
     * Reorders array by hashCode() of its keys.
     *
     * @param array $data
     *
     * @return array
     */
    public static function reorderByHashCode(
        array $data)
    {
        $hashCodes = [];
        foreach ($data as $key => $value) {
            $hashCodes[$key] = self::hashCode($key);
        }

        uksort($data, function ($a, $b) use ($hashCodes) {
            $a = $hashCodes[$a];
            $b = $hashCodes[$b];
            if ($a < $b) {
                return -1;
            } elseif ($a > $b) {
                return 1;
            } else {
                return 0;
            }
        });

        return $data;
    }

    /**
     * Generates random multipart boundary string.
     *
     * @return string
     */
    public static function generateMultipartBoundary()
    {
        $result = '';
        $max = strlen(self::BOUNDARY_CHARS) - 1;
        for ($i = 0; $i < self::BOUNDARY_LENGTH; ++$i) {
            $result .= self::BOUNDARY_CHARS[mt_rand(0, $max)];
        }

        return $result;
    }

    /**
     * Generates user breadcrumb for use when posting a comment.
     *
     * @param int $size
     *
     * @return string
     */
    public static function generateUserBreadcrumb(
        $size)
    {
        $key = 'iN4$aGr0m';
        $date = (int) (microtime(true) * 1000);

        // typing time
        $term = rand(2, 3) * 1000 + $size * rand(15, 20) * 100;

        // android EditText change event occur count
        $text_change_event_count = round($size / rand(2, 3));
        if ($text_change_event_count == 0) {
            $text_change_event_count = 1;
        }

        // generate typing data
        $data = $size.' '.$term.' '.$text_change_event_count.' '.$date;

        return base64_encode(hash_hmac('sha256', $data, $key, true))."\n".base64_encode($data)."\n";
    }

    /**
     * Converts a hours/minutes/seconds timestamp to seconds.
     *
     * @param string $timeStr Either `HH:MM:SS[.###]` (24h-clock) or
     *                        `MM:SS[.###]` or `SS[.###]`. The `[.###]` is for
     *                        optional millisecond precision if wanted, such as
     *                        `00:01:01.149`.
     *
     * @throws \InvalidArgumentException If any part of the input is invalid.
     *
     * @return float The number of seconds, with decimals (milliseconds).
     */
    public static function hmsTimeToSeconds(
        $timeStr)
    {
        if (!is_string($timeStr)) {
            throw new \InvalidArgumentException('Invalid non-string timestamp.');
        }

        $sec = 0.0;
        foreach (array_reverse(explode(':', $timeStr)) as $offsetKey => $v) {
            if ($offsetKey > 2) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid input "%s" with too many components (max 3 is allowed "HH:MM:SS").',
                    $timeStr
                ));
            }

            // Parse component (supports "01" or "01.123" (milli-precision)).
            if ($v === '' || !preg_match('/^\d+(?:\.\d+)?$/', $v)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid non-digit or empty component "%s" in time string "%s".',
                    $v, $timeStr
                ));
            }
            if ($offsetKey !== 0 && strpos($v, '.') !== false) {
                throw new \InvalidArgumentException(sprintf(
                    'Unexpected period in time component "%s" in time string "%s". Only the seconds-component supports milliseconds.',
                    $v, $timeStr
                ));
            }

            // Convert the value to float and cap minutes/seconds to 60 (but
            // allow any number of hours).
            $v = (float) $v;
            $maxValue = $offsetKey < 2 ? 60 : -1;
            if ($maxValue >= 0 && $v > $maxValue) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid time component "%d" (its allowed range is 0-%d) in time string "%s".',
                    $v, $maxValue, $timeStr
                ));
            }

            // Multiply the current component of the "01:02:03" string with the
            // power of its offset. Hour-offset will be 2, Minutes 1 and Secs 0;
            // and "pow(60, 0)" will return 1 which is why seconds work too.
            $sec += pow(60, $offsetKey) * $v;
        }

        return $sec;
    }

    /**
     * Converts seconds to a hours/minutes/seconds timestamp.
     *
     * @param int|float $sec The number of seconds. Can have fractions (millis).
     *
     * @throws \InvalidArgumentException If any part of the input is invalid.
     *
     * @return string The time formatted as `HH:MM:SS.###` (`###` is millis).
     */
    public static function hmsTimeFromSeconds(
        $sec)
    {
        if (!is_int($sec) && !is_float($sec)) {
            throw new \InvalidArgumentException('Seconds must be a number.');
        }

        $wasNegative = false;
        if ($sec < 0) {
            $wasNegative = true;
            $sec = abs($sec);
        }

        $result = sprintf(
            '%02d:%02d:%06.3f', // "%06f" is because it counts the whole string.
            floor($sec / 3600),
            floor(fmod($sec / 60, 60)),
            fmod($sec, 60)
        );

        if ($wasNegative) {
            $result = '-'.$result;
        }

        return $result;
    }

    /**
     * Builds an Instagram media location JSON object in the correct format.
     *
     * This function is used whenever we need to send a location to Instagram's
     * API. All endpoints (so far) expect location data in this exact format.
     *
     * @param Location $location A model object representing the location.
     *
     * @throws \InvalidArgumentException If the location is invalid.
     *
     * @return string The final JSON string ready to submit as an API parameter.
     */
    public static function buildMediaLocationJSON(
        $location)
    {
        if (!$location instanceof Location) {
            throw new \InvalidArgumentException('The location must be an instance of \InstagramAPI\Response\Model\Location.');
        }

        // Forbid locations that came from Location::searchFacebook() and
        // Location::searchFacebookByPoint()! They have slightly different
        // properties, and they don't always contain all data we need. The
        // real application NEVER uses the "Facebook" endpoints for attaching
        // locations to media, and NEITHER SHOULD WE.
        if ($location->getFacebookPlacesId() !== null) {
            throw new \InvalidArgumentException('You are not allowed to use Location model objects from the Facebook-based location search functions. They are not valid media locations!');
        }

        // Core location keys that always exist.
        $obj = [
            'name'            => $location->getName(),
            'lat'             => $location->getLat(),
            'lng'             => $location->getLng(),
            'address'         => $location->getAddress(),
            'external_source' => $location->getExternalIdSource(),
        ];

        // Attach the location ID via a dynamically generated key.
        // NOTE: This automatically generates a key such as "facebook_places_id".
        $key = $location->getExternalIdSource().'_id';
        $obj[$key] = $location->getExternalId();

        // Ensure that all keys are listed in the correct hash order.
        $obj = self::reorderByHashCode($obj);

        return json_encode($obj);
    }

    /**
     * Check for ffprobe dependency.
     *
     * TIP: If your binary isn't findable via the PATH environment locations,
     * you can manually set the correct path to it. Before calling any functions
     * that need FFprobe, you must simply assign a manual value (ONCE) to tell
     * us where to find your FFprobe, like this:
     *
     * \InstagramAPI\Utils::$ffprobeBin = '/home/exampleuser/ffmpeg/bin/ffprobe';
     *
     * @return string|bool Name of the library if present, otherwise FALSE.
     */
    public static function checkFFPROBE()
    {
        // We only resolve this once per session and then cache the result.
        if (self::$ffprobeBin === null) {
            @exec('ffprobe -version 2>&1', $output, $statusCode);
            if ($statusCode === 0) {
                self::$ffprobeBin = 'ffprobe';
            } else {
                self::$ffprobeBin = false; // Nothing found!
            }
        }

        return self::$ffprobeBin;
    }

    /**
     * Verifies a user tag.
     *
     * Ensures that the input strictly contains the exact keys necessary for
     * user tag, and with proper values for them. We cannot validate that the
     * user-id actually exists, but that's the job of the library user!
     *
     * @param mixed $userTag An array containing the user ID and the tag position.
     *                       Example: ['position'=>[0.5,0.5],'user_id'=>'123'].
     *
     * @throws \InvalidArgumentException If the tag is invalid.
     */
    public static function throwIfInvalidUserTag(
        $userTag)
    {
        // NOTE: We can use "array" typehint, but it doesn't give us enough freedom.
        if (!is_array($userTag)) {
            throw new \InvalidArgumentException('User tag must be an array.');
        }

        // Check for required keys.
        $requiredKeys = ['position', 'user_id'];
        $missingKeys = array_diff($requiredKeys, array_keys($userTag));
        if (!empty($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for user tag array.', implode('", "', $missingKeys)));
        }

        // Verify this product tag entry, ensuring that the entry is format
        // ['position'=>[0.0,1.0],'user_id'=>'123'] and nothing else.
        foreach ($userTag as $key => $value) {
            switch ($key) {
                case 'user_id':
                    if (!is_int($value) && !ctype_digit($value)) {
                        throw new \InvalidArgumentException('User ID must be an integer.');
                    }
                    if ($value < 0) {
                        throw new \InvalidArgumentException('User ID must be a positive integer.');
                    }
                    break;
                case 'position':
                    try {
                        self::throwIfInvalidPosition($value);
                    } catch (\InvalidArgumentException $e) {
                        throw new \InvalidArgumentException(sprintf('Invalid user tag position: %s', $e->getMessage()), $e->getCode(), $e);
                    }
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Invalid key "%s" in user tag array.', $key));
            }
        }
    }

    /**
     * Verifies an array of media usertags.
     *
     * Ensures that the input strictly contains the exact keys necessary for
     * usertags, and with proper values for them. We cannot validate that the
     * user-id's actually exist, but that's the job of the library user!
     *
     * @param mixed $usertags The array of usertags, optionally with the "in" or
     *                        "removed" top-level keys holding the usertags. Example:
     *                        ['in'=>[['position'=>[0.5,0.5],'user_id'=>'123'], ...]].
     *
     * @throws \InvalidArgumentException If any tags are invalid.
     */
    public static function throwIfInvalidUsertags(
        $usertags)
    {
        // NOTE: We can use "array" typehint, but it doesn't give us enough freedom.
        if (!is_array($usertags)) {
            throw new \InvalidArgumentException('Usertags must be an array.');
        }

        if (empty($usertags)) {
            throw new \InvalidArgumentException('Empty usertags array.');
        }

        foreach ($usertags as $k => $v) {
            if (!is_array($v)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid usertags array. The value for key "%s" must be an array.', $k
                ));
            }
            // Skip the section if it's empty.
            if (empty($v)) {
                continue;
            }
            // Handle ['in'=>[...], 'removed'=>[...]] top-level keys since
            // this input contained top-level array keys containing the usertags.
            switch ($k) {
                case 'in':
                    foreach ($v as $idx => $userTag) {
                        try {
                            self::throwIfInvalidUserTag($userTag);
                        } catch (\InvalidArgumentException $e) {
                            throw new \InvalidArgumentException(
                                sprintf('Invalid usertag at index "%d": %s', $idx, $e->getMessage()),
                                $e->getCode(),
                                $e
                            );
                        }
                    }
                    break;
                case 'removed':
                    // Check the array of userids to remove.
                    foreach ($v as $userId) {
                        if (!ctype_digit($userId) && (!is_int($userId) || $userId < 0)) {
                            throw new \InvalidArgumentException('Invalid user ID in usertags "removed" array.');
                        }
                    }
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Invalid key "%s" in user tags array.', $k));
            }
        }
    }

    /**
     * Verifies an array of product tags.
     *
     * Ensures that the input strictly contains the exact keys necessary for
     * product tags, and with proper values for them. We cannot validate that the
     * product's-id actually exists, but that's the job of the library user!
     *
     * @param mixed $productTags The array of usertags, optionally with the "in" or
     *                           "removed" top-level keys holding the usertags. Example:
     *                           ['in'=>[['position'=>[0.5,0.5],'product_id'=>'123'], ...]].
     *
     * @throws \InvalidArgumentException If any tags are invalid.
     */
    public static function throwIfInvalidProductTags(
        $productTags)
    {
        // NOTE: We can use "array" typehint, but it doesn't give us enough freedom.
        if (!is_array($productTags)) {
            throw new \InvalidArgumentException('Products tags must be an array.');
        }

        if (empty($productTags)) {
            throw new \InvalidArgumentException('Empty product tags array.');
        }

        foreach ($productTags as $k => $v) {
            if (!is_array($v)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid product tags array. The value for key "%s" must be an array.', $k
                ));
            }

            // Skip the section if it's empty.
            if (empty($v)) {
                continue;
            }

            // Handle ['in'=>[...], 'removed'=>[...]] top-level keys since
            // this input contained top-level array keys containing the product tags.
            switch ($k) {
                case 'in':
                    // Check the array of product tags to insert.
                    foreach ($v as $idx => $productTag) {
                        try {
                            self::throwIfInvalidProductTag($productTag);
                        } catch (\InvalidArgumentException $e) {
                            throw new \InvalidArgumentException(
                                sprintf('Invalid product tag at index "%d": %s', $idx, $e->getMessage()),
                                $e->getCode(),
                                $e
                            );
                        }
                    }
                    break;
                case 'removed':
                    // Check the array of product_id to remove.
                    foreach ($v as $productId) {
                        if (!ctype_digit($productId) && (!is_int($productId) || $productId < 0)) {
                            throw new \InvalidArgumentException('Invalid product ID in product tags "removed" array.');
                        }
                    }
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Invalid key "%s" in product tags array.', $k));
            }
        }
    }

    /**
     * Verifies a product tag.
     *
     * Ensures that the input strictly contains the exact keys necessary for
     * product tag, and with proper values for them. We cannot validate that the
     * product-id actually exists, but that's the job of the library user!
     *
     * @param mixed $productTag An array containing the product ID and the tag position.
     *                          Example: ['position'=>[0.5,0.5],'product_id'=>'123'].
     *
     * @throws \InvalidArgumentException If any tags are invalid.
     */
    public static function throwIfInvalidProductTag(
        $productTag)
    {
        // NOTE: We can use "array" typehint, but it doesn't give us enough freedom.
        if (!is_array($productTag)) {
            throw new \InvalidArgumentException('Product tag must be an array.');
        }

        // Check for required keys.
        $requiredKeys = ['position', 'product_id'];
        $missingKeys = array_diff($requiredKeys, array_keys($productTag));
        if (!empty($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for product tag array.', implode('", "', $missingKeys)));
        }

        // Verify this product tag entry, ensuring that the entry is format
        // ['position'=>[0.0,1.0],'product_id'=>'123'] and nothing else.
        foreach ($productTag as $key => $value) {
            switch ($key) {
                case 'product_id':
                    if (!is_int($value) && !ctype_digit($value)) {
                        throw new \InvalidArgumentException('Product ID must be an integer.');
                    }
                    if ($value < 0) {
                        throw new \InvalidArgumentException('Product ID must be a positive integer.');
                    }
                    break;
                case 'position':
                    try {
                        self::throwIfInvalidPosition($value);
                    } catch (\InvalidArgumentException $e) {
                        throw new \InvalidArgumentException(sprintf('Invalid product tag position: %s', $e->getMessage()), $e->getCode(), $e);
                    }
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Invalid key "%s" in product tag array.', $key));
            }
        }
    }

    /**
     * Verifies a position.
     *
     * @param mixed $position An array containing a position coordinates.
     *
     * @throws \InvalidArgumentException
     */
    public static function throwIfInvalidPosition(
        $position)
    {
        if (!is_array($position)) {
            throw new \InvalidArgumentException('Position must be an array.');
        }

        if (!isset($position[0])) {
            throw new \InvalidArgumentException('X coordinate is required.');
        }
        $x = $position[0];
        if (!is_int($x) && !is_float($x)) {
            throw new \InvalidArgumentException('X coordinate must be a number.');
        }
        if ($x < 0.0 || $x > 1.0) {
            throw new \InvalidArgumentException('X coordinate must be a float between 0.0 and 1.0.');
        }

        if (!isset($position[1])) {
            throw new \InvalidArgumentException('Y coordinate is required.');
        }
        $y = $position[1];
        if (!is_int($y) && !is_float($y)) {
            throw new \InvalidArgumentException('Y coordinate must be a number.');
        }
        if ($y < 0.0 || $y > 1.0) {
            throw new \InvalidArgumentException('Y coordinate must be a float between 0.0 and 1.0.');
        }
    }

    /**
     * Verifies that a single hashtag is valid.
     *
     * This function enforces the following requirements: It must be a string,
     * at least 1 character long, and cannot contain the "#" character itself.
     *
     * @param mixed $hashtag The hashtag to check (should be string but we
     *                       accept anything for checking purposes).
     *
     * @throws \InvalidArgumentException
     */
    public static function throwIfInvalidHashtag(
        $hashtag)
    {
        if (!is_string($hashtag) || !strlen($hashtag)) {
            throw new \InvalidArgumentException('Hashtag must be a non-empty string.');
        }
        // Perform an UTF-8 aware search for the illegal "#" symbol (anywhere).
        // NOTE: We must use mb_strpos() to support international tags.
        if (mb_strpos($hashtag, '#') !== false) {
            throw new \InvalidArgumentException(sprintf(
                'Hashtag "%s" is not allowed to contain the "#" character.',
                $hashtag
            ));
        }
    }

    /**
     * Verifies a rank token.
     *
     * @param string $rankToken
     *
     * @throws \InvalidArgumentException
     */
    public static function throwIfInvalidRankToken(
        $rankToken
    ) {
        if (!Signatures::isValidUUID($rankToken)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid rank token.', $rankToken));
        }
    }

    /**
     * Verifies an array of story poll.
     *
     * @param array[] $storyPoll Array with story poll key-value pairs.
     *
     * @throws \InvalidArgumentException If it's missing keys or has invalid values.
     */
    public static function throwIfInvalidStoryPoll(
        array $storyPoll)
    {
        $requiredKeys = ['question', 'viewer_vote', 'viewer_can_vote', 'tallies', 'is_sticker'];

        if (count($storyPoll) !== 1) {
            throw new \InvalidArgumentException(sprintf('Only one story poll is permitted. You added %d story polls.', count($storyPoll)));
        }

        // Ensure that all keys exist.
        $missingKeys = array_keys(array_diff_key(['question' => 1, 'viewer_vote' => 1, 'viewer_can_vote' => 1, 'tallies' => 1, 'is_sticker' => 1], $storyPoll[0]));
        if (count($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for story poll array.', implode(', ', $missingKeys)));
        }

        foreach ($storyPoll[0] as $k => $v) {
            switch ($k) {
                case 'question':
                    if (!is_string($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story poll array-key "%s".', $v, $k));
                    }
                    break;
                case 'viewer_vote':
                    if ($v !== 0) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story poll array-key "%s".', $v, $k));
                    }
                    break;
                case 'viewer_can_vote':
                case 'is_sticker':
                    if (!is_bool($v) && $v !== true) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story poll array-key "%s".', $v, $k));
                    }
                    break;
                case 'tallies':
                    if (!is_array($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story poll array-key "%s".', $v, $k));
                    }
                    self::_throwIfInvalidStoryPollTallies($v);
                    break;
            }
        }
        self::_throwIfInvalidStoryStickerPlacement(array_diff_key($storyPoll[0], array_flip($requiredKeys)), 'polls');
    }

    /**
     * Verifies an array of story slider.
     *
     * @param array[] $storySlider Array with story slider key-value pairs.
     *
     * @throws \InvalidArgumentException If it's missing keys or has invalid values.
     */
    public static function throwIfInvalidStorySlider(
        array $storySlider)
    {
        $requiredKeys = ['question', 'viewer_vote', 'viewer_can_vote', 'slider_vote_average', 'slider_vote_count', 'emoji', 'background_color', 'text_color', 'is_sticker'];

        if (count($storySlider) !== 1) {
            throw new \InvalidArgumentException(sprintf('Only one story slider is permitted. You added %d story sliders.', count($storySlider)));
        }

        // Ensure that all keys exist.
        $missingKeys = array_keys(array_diff_key(['question' => 1, 'viewer_vote' => 1, 'viewer_can_vote' => 1, 'slider_vote_average' => 1, 'slider_vote_count' => 1, 'emoji' => 1, 'background_color' => 1, 'text_color' => 1, 'is_sticker' => 1], $storySlider[0]));
        if (count($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for story slider array.', implode(', ', $missingKeys)));
        }

        foreach ($storySlider[0] as $k => $v) {
            switch ($k) {
                case 'question':
                    if (!is_string($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story slider array-key "%s".', $v, $k));
                    }
                    break;
                case 'viewer_vote':
                case 'slider_vote_count':
                case 'slider_vote_average':
                    if ($v !== 0) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story slider array-key "%s".', $v, $k));
                    }
                    break;
                case 'background_color':
                case 'text_color':
                    if (!preg_match('/^[0-9a-fA-F]{6}$/', substr($v, 1))) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story slider array-key "%s".', $v, $k));
                    }
                    break;
                case 'emoji':
                    //TODO REQUIRES EMOJI VALIDATION
                    break;
                case 'viewer_can_vote':
                    if (!is_bool($v) && $v !== false) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story poll array-key "%s".', $v, $k));
                    }
                    break;
                case 'is_sticker':
                    if (!is_bool($v) && $v !== true) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story poll array-key "%s".', $v, $k));
                    }
                    break;
            }
        }
        self::_throwIfInvalidStoryStickerPlacement(array_diff_key($storySlider[0], array_flip($requiredKeys)), 'sliders');
    }

    /**
     * Verifies an array of story question.
     *
     * @param array $storyQuestion Array with story question key-value pairs.
     *
     * @throws \InvalidArgumentException If it's missing keys or has invalid values.
     */
    public static function throwIfInvalidStoryQuestion(
        array $storyQuestion)
    {
        $requiredKeys = ['z', 'viewer_can_interact', 'background_color', 'profile_pic_url', 'question_type', 'question', 'text_color', 'is_sticker'];

        if (count($storyQuestion) !== 1) {
            throw new \InvalidArgumentException(sprintf('Only one story question is permitted. You added %d story questions.', count($storyQuestion)));
        }

        // Ensure that all keys exist.
        $missingKeys = array_keys(array_diff_key(['viewer_can_interact' => 1, 'background_color' => 1, 'profile_pic_url' => 1, 'question_type' => 1, 'question' => 1, 'text_color' => 1, 'is_sticker' => 1], $storyQuestion[0]));
        if (count($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for story question array.', implode(', ', $missingKeys)));
        }

        foreach ($storyQuestion[0] as $k => $v) {
            switch ($k) {
                case 'z': // May be used for AR in the future, for now it's always 0.
                    if ($v !== 0) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story question array-key "%s".', $v, $k));
                    }
                    break;
                case 'viewer_can_interact':
                    if (!is_bool($v) || $v !== false) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story question array-key "%s".', $v, $k));
                    }
                    break;
                case 'background_color':
                case 'text_color':
                    if (!preg_match('/^[0-9a-fA-F]{6}$/', substr($v, 1))) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story question array-key "%s".', $v, $k));
                    }
                    break;
                case 'question_type':
                    // At this time only text questions are supported.
                    if (!is_string($v) || $v !== 'text') {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story question array-key "%s".', $v, $k));
                    }
                    break;
                case 'question':
                    if (!is_string($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story question array-key "%s".', $v, $k));
                    }
                    break;
                case 'profile_pic_url':
                    if (!self::hasValidWebURLSyntax($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story question array-key "%s".', $v, $k));
                    }
                    break;
                case 'is_sticker':
                    if (!is_bool($v) && $v !== true) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story question array-key "%s".', $v, $k));
                    }
                    break;
            }
        }
        self::_throwIfInvalidStoryStickerPlacement(array_diff_key($storyQuestion[0], array_flip($requiredKeys)), 'questions');
    }

    /**
     * Verifies an array of story countdown.
     *
     * @param array $storyCountdown Array with story countdown key-value pairs.
     *
     * @throws \InvalidArgumentException If it's missing keys or has invalid values.
     */
    public static function throwIfInvalidStoryCountdown(
        array $storyCountdown)
    {
        $requiredKeys = ['z', 'text', 'text_color', 'start_background_color', 'end_background_color', 'digit_color', 'digit_card_color', 'end_ts', 'following_enabled', 'is_sticker'];

        if (count($storyCountdown) !== 1) {
            throw new \InvalidArgumentException(sprintf('Only one story countdown is permitted. You added %d story countdowns.', count($storyCountdown)));
        }

        // Ensure that all keys exist.
        $missingKeys = array_keys(array_diff_key(['z' => 1, 'text' => 1, 'text_color' => 1, 'start_background_color' => 1, 'end_background_color' => 1, 'digit_color' => 1, 'digit_card_color' => 1, 'end_ts' => 1, 'following_enabled' => 1, 'is_sticker' => 1], $storyCountdown[0]));
        if (count($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for story countdown array.', implode(', ', $missingKeys)));
        }

        foreach ($storyCountdown[0] as $k => $v) {
            switch ($k) {
                case 'z': // May be used for AR in the future, for now it's always 0.
                    if ($v !== 0) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story countdown array-key "%s".', $v, $k));
                    }
                    break;
                case 'text':
                    if (!is_string($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story countdown array-key "%s".', $v, $k));
                    }
                    break;
                case 'text_color':
                case 'start_background_color':
                case 'end_background_color':
                case 'digit_color':
                case 'digit_card_color':
                    if (!preg_match('/^[0-9a-fA-F]{6}$/', substr($v, 1))) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story countdown array-key "%s".', $v, $k));
                    }
                    break;
                case 'end_ts':
                    if (!is_int($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story countdown array-key "%s".', $v, $k));
                    }
                    break;
                case 'following_enabled':
                    if (!is_bool($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story countdown array-key "%s".', $v, $k));
                    }
                    break;
                case 'is_sticker':
                    if (!is_bool($v) && $v !== true) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story countdown array-key "%s".', $v, $k));
                    }
                    break;
            }
        }
        self::_throwIfInvalidStoryStickerPlacement(array_diff_key($storyCountdown[0], array_flip($requiredKeys)), 'countdowns');
    }

    /**
     * Verifies if tallies are valid.
     *
     * @param array[] $tallies Array with story poll key-value pairs.
     *
     * @throws \InvalidArgumentException If it's missing keys or has invalid values.
     */
    protected static function _throwIfInvalidStoryPollTallies(
        array $tallies)
    {
        $requiredKeys = ['text', 'count', 'font_size'];
        if (count($tallies) !== 2) {
            throw new \InvalidArgumentException(sprintf('Missing data for tallies.'));
        }

        foreach ($tallies as $tallie) {
            $missingKeys = array_keys(array_diff_key(['text' => 1, 'count' => 1, 'font_size' => 1], $tallie));

            if (count($missingKeys)) {
                throw new \InvalidArgumentException(sprintf('Missing keys "%s" for location array.', implode(', ', $missingKeys)));
            }
            foreach ($tallie as $k => $v) {
                if (!in_array($k, $requiredKeys, true)) {
                    throw new \InvalidArgumentException(sprintf('Invalid key "%s" for story poll tallies.', $k));
                }
                switch ($k) {
                    case 'text':
                        if (!is_string($v)) {
                            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for tallies array-key "%s".', $v, $k));
                        }
                        break;
                    case 'count':
                        if ($v !== 0) {
                            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for tallies array-key "%s".', $v, $k));
                        }
                        break;
                    case 'font_size':
                        if (!is_float($v) || ($v < 17.5 || $v > 35.0)) {
                            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for tallies array-key "%s".', $v, $k));
                        }
                        break;
                }
            }
        }
    }

    /**
     * Verifies an array of story mentions.
     *
     * @param array[] $storyMentions The array of all story mentions.
     *
     * @throws \InvalidArgumentException If it's missing keys or has invalid values.
     */
    public static function throwIfInvalidStoryMentions(
        array $storyMentions)
    {
        $requiredKeys = ['user_id'];

        foreach ($storyMentions as $mention) {
            // Ensure that all keys exist.
            $missingKeys = array_keys(array_diff_key(['user_id' => 1], $mention));
            if (count($missingKeys)) {
                throw new \InvalidArgumentException(sprintf('Missing keys "%s" for mention array.', implode(', ', $missingKeys)));
            }

            foreach ($mention as $k => $v) {
                switch ($k) {
                    case 'user_id':
                        if (!ctype_digit($v) && (!is_int($v) || $v < 0)) {
                            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for story mention array-key "%s".', $v, $k));
                        }
                        break;
                }
            }
            self::_throwIfInvalidStoryStickerPlacement(array_diff_key($mention, array_flip($requiredKeys)), 'story mentions');
        }
    }

    /**
     * Verifies if a story location sticker is valid.
     *
     * @param array[] $locationSticker Array with location sticker key-value pairs.
     *
     * @throws \InvalidArgumentException If it's missing keys or has invalid values.
     */
    public static function throwIfInvalidStoryLocationSticker(
        array $locationSticker)
    {
        $requiredKeys = ['location_id', 'is_sticker'];
        $missingKeys = array_keys(array_diff_key(['location_id' => 1, 'is_sticker' => 1], $locationSticker));

        if (count($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for location array.', implode(', ', $missingKeys)));
        }

        foreach ($locationSticker as $k => $v) {
            switch ($k) {
                case 'location_id':
                    if (!is_string($v) && !is_numeric($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for location array-key "%s".', $v, $k));
                    }
                    break;
                case 'is_sticker':
                    if (!is_bool($v)) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for hashtag array-key "%s".', $v, $k));
                    }
                    break;
            }
        }
        self::_throwIfInvalidStoryStickerPlacement(array_diff_key($locationSticker, array_flip($requiredKeys)), 'location');
    }

    /**
     * Verifies if a caption is valid for a hashtag and verifies an array of hashtags.
     *
     * @param string  $captionText The caption for the story hashtag to verify.
     * @param array[] $hashtags    The array of all story hashtags.
     *
     * @throws \InvalidArgumentException If caption doesn't contain any hashtag,
     *                                   or if any tags are invalid.
     */
    public static function throwIfInvalidStoryHashtags(
         $captionText,
         array $hashtags)
    {
        $requiredKeys = ['tag_name', 'use_custom_title', 'is_sticker'];

        // Extract all hashtags from the caption using a UTF-8 aware regex.
        if (!preg_match_all('/#(\w+[^\x00-\x7F]?+)/u', $captionText, $tagsInCaption)) {
            throw new \InvalidArgumentException('Invalid caption for hashtag.');
        }

        // Verify all provided hashtags.
        foreach ($hashtags as $hashtag) {
            $missingKeys = array_keys(array_diff_key(['tag_name' => 1, 'use_custom_title' => 1, 'is_sticker' => 1], $hashtag));
            if (count($missingKeys)) {
                throw new \InvalidArgumentException(sprintf('Missing keys "%s" for hashtag array.', implode(', ', $missingKeys)));
            }

            foreach ($hashtag as $k => $v) {
                switch ($k) {
                    case 'tag_name':
                        // Ensure that the hashtag format is valid.
                        self::throwIfInvalidHashtag($v);
                        // Verify that this tag exists somewhere in the caption to check.
                        if (!in_array($v, $tagsInCaption[1])) { // NOTE: UTF-8 aware.
                            throw new \InvalidArgumentException(sprintf('Tag name "%s" does not exist in the caption text.', $v));
                        }
                        break;
                    case 'use_custom_title':
                        if (!is_bool($v)) {
                            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for hashtag array-key "%s".', $v, $k));
                        }
                        break;
                    case 'is_sticker':
                        if (!is_bool($v)) {
                            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for hashtag array-key "%s".', $v, $k));
                        }
                        break;
                }
            }
            self::_throwIfInvalidStoryStickerPlacement(array_diff_key($hashtag, array_flip($requiredKeys)), 'hashtag');
        }
    }

    /**
     * Verifies an attached media.
     *
     * @param array[] $attachedMedia Array containing the attached media data.
     *
     * @throws \InvalidArgumentException If it's missing keys or has invalid values.
     */
    public static function throwIfInvalidAttachedMedia(
        array $attachedMedia)
    {
        $attachedMedia = reset($attachedMedia);
        $requiredKeys = ['media_id', 'is_sticker'];

        // Ensure that all keys exist.
        $missingKeys = array_keys(array_diff_key(['media_id' => 1, 'is_sticker' => 1], $attachedMedia));
        if (count($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for attached media.', implode(', ', $missingKeys)));
        }

        if (!is_string($attachedMedia['media_id']) && !is_numeric($attachedMedia['media_id'])) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for media_id.', $attachedMedia['media_id']));
        }

        if (!is_bool($attachedMedia['is_sticker']) && $attachedMedia['is_sticker'] !== true) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for attached media.', $attachedMedia['is_sticker']));
        }

        self::_throwIfInvalidStoryStickerPlacement(array_diff_key($attachedMedia, array_flip($requiredKeys)), 'attached media');
    }

    /**
     * Verifies a story sticker's placement parameters.
     *
     * There are many kinds of story stickers, such as hashtags, locations,
     * mentions, etc. To place them on the media, the user must provide certain
     * parameters for things like position and size. This function verifies all
     * of those parameters and ensures that the sticker placement is valid.
     *
     * @param array  $storySticker The array describing the story sticker placement.
     * @param string $type         What type of sticker this is.
     *
     * @throws \InvalidArgumentException If storySticker is missing keys or has invalid values.
     */
    protected static function _throwIfInvalidStoryStickerPlacement(
        array $storySticker,
        $type)
    {
        $requiredKeys = ['x', 'y', 'width', 'height', 'rotation'];

        // Ensure that all required hashtag array keys exist.
        $missingKeys = array_keys(array_diff_key(['x' => 1, 'y' => 1, 'width' => 1, 'height' => 1, 'rotation' => 0], $storySticker));
        if (count($missingKeys)) {
            throw new \InvalidArgumentException(sprintf('Missing keys "%s" for "%s".', implode(', ', $missingKeys), $type));
        }

        // Check the individual array values.
        foreach ($storySticker as $k => $v) {
            if (!in_array($k, $requiredKeys, true)) {
                throw new \InvalidArgumentException(sprintf('Invalid key "%s" for "%s".', $k, $type));
            }
            switch ($k) {
                case 'x':
                case 'y':
                case 'width':
                case 'height':
                case 'rotation':
                    if (!is_float($v) || $v < 0.0 || $v > 1.0) {
                        throw new \InvalidArgumentException(sprintf('Invalid value "%s" for "%s" key "%s".', $v, $type, $k));
                    }
                    break;
            }
        }
    }

    /**
     * Checks and validates a media item's type.
     *
     * @param string|int $mediaType The type of the media item. One of: "PHOTO", "VIDEO"
     *                              "CAROUSEL", or the raw value of the Item's "getMediaType()" function.
     *
     * @throws \InvalidArgumentException If the type is invalid.
     *
     * @return string The verified final type; either "PHOTO", "VIDEO" or "CAROUSEL".
     */
    public static function checkMediaType(
        $mediaType)
    {
        if (ctype_digit($mediaType) || is_int($mediaType)) {
            if ($mediaType == Item::PHOTO) {
                $mediaType = 'PHOTO';
            } elseif ($mediaType == Item::VIDEO) {
                $mediaType = 'VIDEO';
            } elseif ($mediaType == Item::CAROUSEL) {
                $mediaType = 'CAROUSEL';
            }
        }
        if (!in_array($mediaType, ['PHOTO', 'VIDEO', 'CAROUSEL'], true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid media type.', $mediaType));
        }

        return $mediaType;
    }

    public static function formatBytes(
        $bytes,
        $precision = 2)
    {
        $units = ['B', 'kB', 'mB', 'gB', 'tB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).''.$units[$pow];
    }

    public static function colouredString(
        $string,
        $colour)
    {
        $colours['black'] = '0;30';
        $colours['dark_gray'] = '1;30';
        $colours['blue'] = '0;34';
        $colours['light_blue'] = '1;34';
        $colours['green'] = '0;32';
        $colours['light_green'] = '1;32';
        $colours['cyan'] = '0;36';
        $colours['light_cyan'] = '1;36';
        $colours['red'] = '0;31';
        $colours['light_red'] = '1;31';
        $colours['purple'] = '0;35';
        $colours['light_purple'] = '1;35';
        $colours['brown'] = '0;33';
        $colours['yellow'] = '1;33';
        $colours['light_gray'] = '0;37';
        $colours['white'] = '1;37';

        $colored_string = '';

        if (isset($colours[$colour])) {
            $colored_string .= "\033[".$colours[$colour].'m';
        }

        $colored_string .= $string."\033[0m";

        return $colored_string;
    }

    public static function getFilterCode(
        $filter)
    {
        $filters = [];
        $filters[0] = 'Normal';
        $filters[615] = 'Lark';
        $filters[614] = 'Reyes';
        $filters[613] = 'Juno';
        $filters[612] = 'Aden';
        $filters[608] = 'Perpetua';
        $filters[603] = 'Ludwig';
        $filters[605] = 'Slumber';
        $filters[616] = 'Crema';
        $filters[24] = 'Amaro';
        $filters[17] = 'Mayfair';
        $filters[23] = 'Rise';
        $filters[26] = 'Hudson';
        $filters[25] = 'Valencia';
        $filters[1] = 'X-Pro II';
        $filters[27] = 'Sierra';
        $filters[28] = 'Willow';
        $filters[2] = 'Lo-Fi';
        $filters[3] = 'Earlybird';
        $filters[22] = 'Brannan';
        $filters[10] = 'Inkwell';
        $filters[21] = 'Hefe';
        $filters[15] = 'Nashville';
        $filters[18] = 'Sutro';
        $filters[19] = 'Toaster';
        $filters[20] = 'Walden';
        $filters[14] = '1977';
        $filters[16] = 'Kelvin';
        $filters[-2] = 'OES';
        $filters[-1] = 'YUV';
        $filters[109] = 'Stinson';
        $filters[106] = 'Vesper';
        $filters[112] = 'Clarendon';
        $filters[118] = 'Maven';
        $filters[114] = 'Gingham';
        $filters[107] = 'Ginza';
        $filters[113] = 'Skyline';
        $filters[105] = 'Dogpatch';
        $filters[115] = 'Brooklyn';
        $filters[111] = 'Moon';
        $filters[117] = 'Helena';
        $filters[116] = 'Ashby';
        $filters[108] = 'Charmes';
        $filters[640] = 'BrightContrast';
        $filters[642] = 'CrazyColor';
        $filters[643] = 'SubtleColor';

        return array_search($filter, $filters);
    }

    /**
     * Creates a folder if missing, or ensures that it is writable.
     *
     * @param string $folder The directory path.
     *
     * @return bool TRUE if folder exists and is writable, otherwise FALSE.
     */
    public static function createFolder(
        $folder)
    {
        // Test write-permissions for the folder and create/fix if necessary.
        if ((is_dir($folder) && is_writable($folder))
            || (!is_dir($folder) && mkdir($folder, 0755, true))
            || chmod($folder, 0755)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Recursively deletes a file/directory tree.
     *
     * @param string $folder         The directory path.
     * @param bool   $keepRootFolder Whether to keep the top-level folder.
     *
     * @return bool TRUE on success, otherwise FALSE.
     */
    public static function deleteTree(
        $folder,
        $keepRootFolder = false)
    {
        // Handle bad arguments.
        if (empty($folder) || !file_exists($folder)) {
            return true; // No such file/folder exists.
        } elseif (is_file($folder) || is_link($folder)) {
            return @unlink($folder); // Delete file/link.
        }

        // Delete all children.
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            if (!@$action($fileinfo->getRealPath())) {
                return false; // Abort due to the failure.
            }
        }

        // Delete the root folder itself?
        return !$keepRootFolder ? @rmdir($folder) : true;
    }

    /**
     * Atomic filewriter.
     *
     * Safely writes new contents to a file using an atomic two-step process.
     * If the script is killed before the write is complete, only the temporary
     * trash file will be corrupted.
     *
     * The algorithm also ensures that 100% of the bytes were written to disk.
     *
     * @param string $filename     Filename to write the data to.
     * @param string $data         Data to write to file.
     * @param string $atomicSuffix Lets you optionally provide a different
     *                             suffix for the temporary file.
     *
     * @return int|bool Number of bytes written on success, otherwise `FALSE`.
     */
    public static function atomicWrite(
        $filename,
        $data,
        $atomicSuffix = 'atomictmp')
    {
        // Perform an exclusive (locked) overwrite to a temporary file.
        $filenameTmp = sprintf('%s.%s', $filename, $atomicSuffix);
        $writeResult = @file_put_contents($filenameTmp, $data, LOCK_EX);

        // Only proceed if we wrote 100% of the data bytes to disk.
        if ($writeResult !== false && $writeResult === strlen($data)) {
            // Now move the file to its real destination (replaces if exists).
            $moveResult = @rename($filenameTmp, $filename);
            if ($moveResult === true) {
                // Successful write and move. Return number of bytes written.
                return $writeResult;
            }
        }

        // We've failed. Remove the temporary file if it exists.
        if (is_file($filenameTmp)) {
            @unlink($filenameTmp);
        }

        return false; // Failed.
    }

    /**
     * Creates an empty temp file with a unique filename.
     *
     * @param string $outputDir  Folder to place the temp file in.
     * @param string $namePrefix (optional) What prefix to use for the temp file.
     *
     * @throws \RuntimeException If the file cannot be created.
     *
     * @return string
     */
    public static function createTempFile(
        $outputDir,
        $namePrefix = 'TEMP')
    {
        // Automatically generates a name like "INSTATEMP_" or "INSTAVID_" etc.
        $finalPrefix = sprintf('INSTA%s_', $namePrefix);

        // Try to create the file (detects errors).
        $tmpFile = @tempnam($outputDir, $finalPrefix);
        if (!is_string($tmpFile)) {
            throw new \RuntimeException(sprintf(
                'Unable to create temporary output file in "%s" (with prefix "%s").',
                $outputDir, $finalPrefix
            ));
        }

        return $tmpFile;
    }

    /**
     * Closes a file pointer if it's open.
     *
     * Always use this function instead of fclose()!
     *
     * Unlike the normal fclose(), this function is safe to call multiple times
     * since it only attempts to close the pointer if it's actually still open.
     * The normal fclose() would give an annoying warning in that scenario.
     *
     * @param resource $handle A file pointer opened by fopen() or fsockopen().
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public static function safe_fclose(
        $handle)
    {
        if (is_resource($handle)) {
            return fclose($handle);
        }

        return true;
    }

    /**
     * Checks if a URL has valid "web" syntax.
     *
     * This function is Unicode-aware.
     *
     * Be aware that it only performs URL syntax validation! It doesn't check
     * if the domain/URL is fully valid and actually reachable!
     *
     * It verifies that the URL begins with either the "http://" or "https://"
     * protocol, and that it must contain a host with at least one period in it,
     * and at least two characters after the period (in other words, a TLD). The
     * rest of the string can be any sequence of non-whitespace characters.
     *
     * For example, "http://localhost" will not be seen as a valid web URL, and
     * "http://www.google.com foobar" is not a valid web URL since there's a
     * space in it. But "https://bing.com" and "https://a.com/foo" are valid.
     * However, "http://a.abdabdbadbadbsa" is also seen as a valid URL, since
     * the validation is pretty simple and doesn't verify the TLDs (there are
     * too many now to catch them all and new ones appear constantly).
     *
     * @param string $url
     *
     * @return bool TRUE if valid web syntax, otherwise FALSE.
     */
    public static function hasValidWebURLSyntax(
        $url)
    {
        return (bool) preg_match('/^https?:\/\/[^\s.\/]+\.[^\s.\/]{2}\S*$/iu', $url);
    }

    /**
     * Extract all URLs from a text string.
     *
     * This function is Unicode-aware.
     *
     * @param string $text The string to scan for URLs.
     *
     * @return array An array of URLs and their individual components.
     */
    public static function extractURLs(
        $text)
    {
        $urls = [];
        if (preg_match_all(
            // NOTE: This disgusting regex comes from the Android SDK, slightly
            // modified by Instagram and then encoded by us into PHP format. We
            // are NOT allowed to tweak this regex! It MUST match the official
            // app so that our link-detection acts *exactly* like the real app!
            // NOTE: Here is the "to PHP regex" conversion algorithm we used:
            // https://github.com/mgp25/Instagram-API/issues/1445#issuecomment-318921867
            '/((?:(http|https|Http|Https|rtsp|Rtsp):\/\/(?:(?:[a-zA-Z0-9$\-\_\.\+\!\*\'\(\)\,\;\?\&\=]|(?:\%[a-fA-F0-9]{2})){1,64}(?:\:(?:[a-zA-Z0-9$\-\_\.\+\!\*\'\(\)\,\;\?\&\=]|(?:\%[a-fA-F0-9]{2})){1,25})?\@)?)?((?:(?:[a-zA-Z0-9\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}\_][a-zA-Z0-9\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}\_\-]{0,64}\.)+(?:(?:aero|arpa|asia|a[cdefgilmnoqrstuwxz])|(?:biz|b[abdefghijmnorstvwyz])|(?:cat|com|coop|c[acdfghiklmnoruvxyz])|d[ejkmoz]|(?:edu|e[cegrstu])|f[ijkmor]|(?:gov|g[abdefghilmnpqrstuwy])|h[kmnrtu]|(?:info|int|i[delmnoqrst])|(?:jobs|j[emop])|k[eghimnprwyz]|l[abcikrstuvy]|(?:mil|mobi|museum|m[acdeghklmnopqrstuvwxyz])|(?:name|net|n[acefgilopruz])|(?:org|om)|(?:pro|p[aefghklmnrstwy])|qa|r[eosuw]|s[abcdeghijklmnortuvyz]|(?:tel|travel|t[cdfghjklmnoprtvwz])|u[agksyz]|v[aceginu]|w[fs]|(?:\x{03B4}\x{03BF}\x{03BA}\x{03B9}\x{03BC}\x{03AE}|\x{0438}\x{0441}\x{043F}\x{044B}\x{0442}\x{0430}\x{043D}\x{0438}\x{0435}|\x{0440}\x{0444}|\x{0441}\x{0440}\x{0431}|\x{05D8}\x{05E2}\x{05E1}\x{05D8}|\x{0622}\x{0632}\x{0645}\x{0627}\x{06CC}\x{0634}\x{06CC}|\x{0625}\x{062E}\x{062A}\x{0628}\x{0627}\x{0631}|\x{0627}\x{0644}\x{0627}\x{0631}\x{062F}\x{0646}|\x{0627}\x{0644}\x{062C}\x{0632}\x{0627}\x{0626}\x{0631}|\x{0627}\x{0644}\x{0633}\x{0639}\x{0648}\x{062F}\x{064A}\x{0629}|\x{0627}\x{0644}\x{0645}\x{063A}\x{0631}\x{0628}|\x{0627}\x{0645}\x{0627}\x{0631}\x{0627}\x{062A}|\x{0628}\x{06BE}\x{0627}\x{0631}\x{062A}|\x{062A}\x{0648}\x{0646}\x{0633}|\x{0633}\x{0648}\x{0631}\x{064A}\x{0629}|\x{0641}\x{0644}\x{0633}\x{0637}\x{064A}\x{0646}|\x{0642}\x{0637}\x{0631}|\x{0645}\x{0635}\x{0631}|\x{092A}\x{0930}\x{0940}\x{0915}\x{094D}\x{0937}\x{093E}|\x{092D}\x{093E}\x{0930}\x{0924}|\x{09AD}\x{09BE}\x{09B0}\x{09A4}|\x{0A2D}\x{0A3E}\x{0A30}\x{0A24}|\x{0AAD}\x{0ABE}\x{0AB0}\x{0AA4}|\x{0B87}\x{0BA8}\x{0BCD}\x{0BA4}\x{0BBF}\x{0BAF}\x{0BBE}|\x{0B87}\x{0BB2}\x{0B99}\x{0BCD}\x{0B95}\x{0BC8}|\x{0B9A}\x{0BBF}\x{0B99}\x{0BCD}\x{0B95}\x{0BAA}\x{0BCD}\x{0BAA}\x{0BC2}\x{0BB0}\x{0BCD}|\x{0BAA}\x{0BB0}\x{0BBF}\x{0B9F}\x{0BCD}\x{0B9A}\x{0BC8}|\x{0C2D}\x{0C3E}\x{0C30}\x{0C24}\x{0C4D}|\x{0DBD}\x{0D82}\x{0D9A}\x{0DCF}|\x{0E44}\x{0E17}\x{0E22}|\x{30C6}\x{30B9}\x{30C8}|\x{4E2D}\x{56FD}|\x{4E2D}\x{570B}|\x{53F0}\x{6E7E}|\x{53F0}\x{7063}|\x{65B0}\x{52A0}\x{5761}|\x{6D4B}\x{8BD5}|\x{6E2C}\x{8A66}|\x{9999}\x{6E2F}|\x{D14C}\x{C2A4}\x{D2B8}|\x{D55C}\x{AD6D}|xn\-\-0zwm56d|xn\-\-11b5bs3a9aj6g|xn\-\-3e0b707e|xn\-\-45brj9c|xn\-\-80akhbyknj4f|xn\-\-90a3ac|xn\-\-9t4b11yi5a|xn\-\-clchc0ea0b2g2a9gcd|xn\-\-deba0ad|xn\-\-fiqs8s|xn\-\-fiqz9s|xn\-\-fpcrj9c3d|xn\-\-fzc2c9e2c|xn\-\-g6w251d|xn\-\-gecrj9c|xn\-\-h2brj9c|xn\-\-hgbk6aj7f53bba|xn\-\-hlcj6aya9esc7a|xn\-\-j6w193g|xn\-\-jxalpdlp|xn\-\-kgbechtv|xn\-\-kprw13d|xn\-\-kpry57d|xn\-\-lgbbat1ad8j|xn\-\-mgbaam7a8h|xn\-\-mgbayh7gpa|xn\-\-mgbbh1a71e|xn\-\-mgbc0a9azcg|xn\-\-mgberp4a5d4ar|xn\-\-o3cw4h|xn\-\-ogbpf8fl|xn\-\-p1ai|xn\-\-pgbs0dh|xn\-\-s9brj9c|xn\-\-wgbh1c|xn\-\-wgbl6a|xn\-\-xkc2al3hye2a|xn\-\-xkc2dl3a5ee0h|xn\-\-yfro4i67o|xn\-\-ygbi2ammx|xn\-\-zckzah|xxx)|y[et]|z[amw]))|(?:(?:25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[1-9][0-9]|[1-9])\.(?:25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[1-9][0-9]|[1-9]|0)\.(?:25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[1-9][0-9]|[1-9]|0)\.(?:25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[1-9][0-9]|[0-9])))(?:\:\d{1,5})?)(\/(?:(?:[a-zA-Z0-9\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}\;\/\?\:\@\&\=\#\~\-\.\+\!\*\'\(\)\,\_])|(?:\%[a-fA-F0-9]{2}))*)?(?:\b|$)/iu',
            $text,
            $matches,
            PREG_SET_ORDER
        ) !== false) {
            foreach ($matches as $match) {
                $urls[] = [
                    'fullUrl'  => $match[0], // "https://foo:bar@www.bing.com/?foo=#test"
                    'baseUrl'  => $match[1], // "https://foo:bar@www.bing.com"
                    'protocol' => $match[2], // "https" (empty if no protocol)
                    'domain'   => $match[3], // "www.bing.com"
                    'path'     => isset($match[4]) ? $match[4] : '', // "/?foo=#test"
                ];
            }
        }

        return $urls;
    }
}
