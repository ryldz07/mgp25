<?php

namespace InstagramAPI;

/**
 * Class for converting media IDs to/from Instagram's shortcode system.
 *
 * The shortcode is the https://instagram.com/p/SHORTCODE/ part of the URL.
 * There are many reasons why you would want to be able to convert back and
 * forth between shortcodes and internal ID numbers. This library helps you!
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class InstagramID
{
    /**
     * Base64 URL Safe Character Map.
     *
     * This is the Base64 "URL Safe" alphabet, which is what Instagram uses.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc4648
     */
    const BASE64URL_CHARMAP = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

    /**
     * Internal map of the results of all base10 digits (0-9) modulo 2.
     *
     * Used by the decimal-to-binary converter, to avoid costly bcmod() calls.
     * Arranged by decimal offset, so the answer for decimal 9 is in index 9.
     *
     * @var string
     */
    const BASE10_MOD2 = ['0', '1', '0', '1', '0', '1', '0', '1', '0', '1'];

    /**
     * Runtime cached bit-value lookup table.
     *
     * @var array|null
     */
    public static $bitValueTable = null;

    /**
     * Converts an Instagram ID to their shortcode system.
     *
     * @param string|int $id The ID to convert. Must be provided as a string if
     *                       it's larger than the size of an integer, which MOST
     *                       Instagram IDs are!
     *
     * @throws \InvalidArgumentException If bad parameters are provided.
     *
     * @return string The shortcode.
     */
    public static function toCode(
        $id)
    {
        // First we must convert the ID number to a binary string.
        // NOTE: Conversion speed depends on number size. With the most common
        // number size used for Instagram's IDs, my old laptop can do ~18k/s.
        $base2 = self::base10to2($id, false); // No left-padding. Throws if bad.
        if ($base2 === '') {
            return ''; // Nothing to convert.
        }

        // Left-pad with leading zeroes to make length a multiple of 6 bits.
        $padAmount = (6 - (strlen($base2) % 6));
        if ($padAmount != 6 || strlen($base2) === 0) {
            $base2 = str_repeat('0', $padAmount).$base2;
        }

        // Now chunk it in segments of 6 bits at a time. Every 6 "digits" in a
        // binary number is just 1 "digit" in a base64 number, because base64
        // can represent the values 0-63, and 63 is "111111" (6 bits) in base2.
        // Example: 9999 base10 = 10 011100 001111 base2 = (2, 28, 15) base64.
        $chunks = str_split($base2, 6);

        // Process and encode all chunks as base64 using Instagram's alphabet.
        $encoded = '';
        foreach ($chunks as $chunk) {
            // Interpret the chunk bitstring as an unsigned integer (0-63).
            $base64 = bindec($chunk);

            // Look up that base64 character in Instagram's alphabet.
            $encoded .= self::BASE64URL_CHARMAP[$base64];
        }

        return $encoded;
    }

    /**
     * Converts an Instagram shortcode to a numeric ID.
     *
     * @param string $code The shortcode.
     *
     * @throws \InvalidArgumentException If bad parameters are provided.
     *
     * @return string The numeric ID.
     */
    public static function fromCode(
        $code)
    {
        if (!is_string($code) || preg_match('/[^A-Za-z0-9\-_]/', $code)) {
            throw new \InvalidArgumentException('Input must be a valid Instagram shortcode.');
        }

        // Convert the base64 shortcode to a base2 binary string.
        $base2 = '';
        for ($i = 0, $len = strlen($code); $i < $len; ++$i) {
            // Find the base64 value of the current character.
            $base64 = strpos(self::BASE64URL_CHARMAP, $code[$i]);

            // Convert it to 6 binary bits (left-padded if needed).
            $base2 .= str_pad(decbin($base64), 6, '0', STR_PAD_LEFT);
        }

        // Now just convert the base2 binary string to a base10 decimal string.
        $base10 = self::base2to10($base2);

        return $base10;
    }

    /**
     * Converts a decimal number of any size into a binary string.
     *
     * @param string|int $base10  The number to convert, in base 10. Must be 0+,
     *                            a positive integer. And you must pass the
     *                            number as a string if you want to convert a
     *                            number that's larger than what fits in your
     *                            CPU's integer size.
     * @param bool       $padLeft Whether to pad with leading zeroes.
     *
     * @throws \InvalidArgumentException If the input isn't a valid integer.
     *
     * @return string The binary bits as a string.
     */
    public static function base10to2(
        $base10,
        $padLeft = true)
    {
        $base10 = (string) $base10;
        if ($base10 === '' || preg_match('/[^0-9]/', $base10)) {
            throw new \InvalidArgumentException('Input must be a positive integer.');
        }

        // Convert the arbitrary-length base10 input to a base2 binary string.
        // We process as strings to support unlimited input number sizes!
        $base2 = '';
        do {
            // Get the last digit.
            $lastDigit = $base10[(strlen($base10) - 1)];

            // If the last digit is uneven, put a one (1) in the base2 string,
            // otherwise use zero (0) instead. Array is 10x faster than bcmod.
            $base2 .= self::BASE10_MOD2[$lastDigit];

            // Now divide the whole base10 string by two, discarding decimals.
            // NOTE: Division is unavoidable when converting decimal to binary,
            // but at least it's implemented in pure C thanks to the BC library.
            // An implementation of arbitrary-length division by 2 in just PHP
            // was ~4x slower. Anyway, my old laptop can do ~1.6 million bcdiv()
            // per second so this is no problem.
            $base10 = bcdiv($base10, '2', 0);
        } while ($base10 !== '0');

        // We built the base2 string backwards, so now we must reverse it.
        $base2 = strrev($base2);

        // Add or remove proper left-padding with zeroes as needed.
        if ($padLeft) {
            $padAmount = (8 - (strlen($base2) % 8));
            if ($padAmount != 8 || strlen($base2) === 0) {
                $base2 = str_repeat('0', $padAmount).$base2;
            }
        } else {
            $base2 = ltrim($base2, '0');
        }

        return $base2;
    }

    /**
     * Builds a binary bit-value lookup table.
     *
     * @param int $maxBitCount Maximum number of bits to calculate values for.
     *
     * @return array The lookup table, where offset 0 has the value of bit 1,
     *               offset 1 has the value of bit 2, and so on.
     */
    public static function buildBinaryLookupTable(
        $maxBitCount)
    {
        $table = [];
        for ($bitPosition = 0; $bitPosition < $maxBitCount; ++$bitPosition) {
            $bitValue = bcpow('2', (string) $bitPosition, 0);
            $table[] = $bitValue;
        }

        return $table;
    }

    /**
     * Converts a binary number of any size into a decimal string.
     *
     * @param string $base2 The binary bits as a string where each character is
     *                      either "1" or "0".
     *
     * @throws \InvalidArgumentException If the input isn't a binary string.
     *
     * @return string The decimal number as a string.
     */
    public static function base2to10(
        $base2)
    {
        if (!is_string($base2) || preg_match('/[^01]/', $base2)) {
            throw new \InvalidArgumentException('Input must be a binary string.');
        }

        // Pre-build a ~80kb RAM table with all values for bits 1-512. Any
        // higher bits than that will be generated and cached live instead.
        if (self::$bitValueTable === null) {
            self::$bitValueTable = self::buildBinaryLookupTable(512);
        }

        // Reverse the bit-sequence so that the least significant bit is first,
        // which is necessary when converting binary via its bit offset powers.
        $base2rev = strrev($base2);

        // Process each bit individually and reconstruct the base10 number.
        $base10 = '0';
        $bits = str_split($base2rev, 1);
        for ($bitPosition = 0, $len = count($bits); $bitPosition < $len; ++$bitPosition) {
            if ($bits[$bitPosition] == '1') {
                // Look up the bit value in the table or generate if missing.
                if (isset(self::$bitValueTable[$bitPosition])) {
                    $bitValue = self::$bitValueTable[$bitPosition];
                } else {
                    $bitValue = bcpow('2', (string) $bitPosition, 0);
                    self::$bitValueTable[$bitPosition] = $bitValue;
                }

                // Now just add the bit's value to the current total.
                $base10 = bcadd($base10, $bitValue, 0);
            }
        }

        return $base10;
    }
}
