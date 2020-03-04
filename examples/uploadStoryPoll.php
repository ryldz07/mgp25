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

// NOTE: This code will make a story poll sticker with the two answers provided,
// but YOU need to manually draw the question on top of your image yourself
// before uploading if you want the question to be visible.

// Now create the metadata array:
$metadata = [
    'story_polls' => [
        // Note that you can only do one story poll in this array.
        [
            'question'         => 'Is this API great?', // Story poll question. You need to manually to draw it on top of your image.
            'viewer_vote'      => 0, // Don't change this value.
            'viewer_can_vote'  => true, // Don't change this value.
            'tallies'          => [
                [
                    'text'      => 'Best API!', // Answer 1.
                    'count'     => 0, // Don't change this value.
                    'font_size' => 35.0, // Range: 17.5 - 35.0.
                ],
                [
                    'text'      => 'The doubt offends', // Answer 2.
                    'count'     => 0, // Don't change this value.
                    'font_size' => 27.5, // Range: 17.5 - 35.0.
                ],
            ],
            'x'                => 0.5, // Range: 0.0 - 1.0. Note that x = 0.5 and y = 0.5 is center of screen.
            'y'                => 0.5, // Also note that X/Y is setting the position of the CENTER of the clickable area.
            'width'            => 0.5661107, // Clickable area size, as percentage of image size: 0.0 - 1.0
            'height'           => 0.10647108, // ...
            'rotation'         => 0.0,
            'is_sticker'       => true, // Don't change this value.
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
