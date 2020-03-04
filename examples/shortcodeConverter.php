<?php

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

use InstagramAPI\InstagramID;

/*
 * This example shows how to use the InstagramID class to convert
 * Media IDs to and from Instagram's shortcode format.
 *
 * The shortcode is the https://instagram.com/p/SHORTCODE/ part of the URL.
 */

// An instagram media ID, provided as a STRING (surrounded by ""). Do NOT use an
// integer. Instagram's IDs are TOO LARGE to fit in your CPU's int bitsize!
$input_id = '1469339037185548405';

echo "[INSTAGRAM ID CONVERSION]\n\n";

// From Media ID to Shortcode.
$code = InstagramID::toCode($input_id);
echo "Instagram id({$input_id}) -> code({$code})\n\n";

// From Shortcode to Media ID.
$id = InstagramID::fromCode($code);
echo "Instagram code({$code}) -> id({$id})\n\n";
