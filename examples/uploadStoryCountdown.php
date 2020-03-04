<?php

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

/////// CONFIG ///////
$username = '';
$password = '';
$debug = true;
$truncatedDebug = false;
//////////////////////

/////// MEDIA ////////
$photoFilename = '';
//////////////////////

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}

// Now create the metadata array:
$metadata = [
    'story_countdowns' => [
        [
            'x'                      => 0.5, // Range: 0.0 - 1.0. Note that x = 0.5 and y = 0.5 is center of screen.
            'y'                      => 0.5, // Also note that X/Y is setting the position of the CENTER of the clickable area.
            'z'                      => 0, // Don't change this value.
            'width'                  => 0.6564815, // Clickable area size, as percentage of image size: 0.0 - 1.0
            'height'                 => 0.22630993, // ...
            'rotation'               => 0.0,
            'text'                   => 'NEXT API RELEASE', // Name of the countdown. Make sure it's in all caps!
            'text_color'             => '#ffffff',
            'start_background_color' => '#ca2ee1',
            'end_background_color'   => '#5eb1ff',
            'digit_color'            => '#7e0091',
            'digit_card_color'       => '#ffffff',
            'end_ts'                 => time() + strtotime('1 day', 0), // UNIX Epoch of when the countdown expires.
            'following_enabled'      => true, // If true, viewers can subscribe to a notification when the countdown expires.
            'is_sticker'             => true, // Don't change this value.
        ],
    ],
];

try {
    // This example will upload the image via our automatic photo processing
    // class. It will ensure that the story file matches the ~9:16 (portrait)
    // aspect ratio needed by Instagram stories. You have nothing to worry
    // about, since the class uses temporary files if the input needs
    // processing, and it never overwrites your original file.
    //
    // Also note that it has lots of options, so read its class documentation!
    $photo = new \InstagramAPI\Media\Photo\InstagramPhoto($photoFilename, ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);
    $ig->story->uploadPhoto($photo->getFile(), $metadata);

    // NOTE: Providing metadata for story uploads is OPTIONAL. If you just want
    // to upload it without any tags/location/caption, simply do the following:
    // $ig->story->uploadPhoto($photo->getFile());
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}
