<?php

use InstagramAPI\Utils;

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

/////// CONFIG ///////
$username = '';
$password = '';
$debug = true;
$truncatedDebug = false;
//////////////////////

/////// MEDIA ID ////////
$mediaId = '1772128724164452834'; // Only Media PK for this scenario.
//////////////////////

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}

// NOTE: This code will make the credits of the media area 'clickable', but YOU need to
// manually draw the credit to the user or a sticker-image on top of your image yourself
// before uploading, if you want the credit to actually be visible on-screen!

// If we want to attach a media, we must find a valid media_id first.
try {
    $mediaInfo = $ig->media->getInfo($mediaId)->getItems()[0];
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}

// Now create the metadata array:
$metadata = [
    'attached_media' => [
        [
            'media_id'         => $mediaId,
            'x'                => 0.5, // Range: 0.0 - 1.0. Note that x = 0.5 and y = 0.5 is center of screen.
            'y'                => 0.5, // Also note that X/Y is setting the position of the CENTER of the clickable area.
            'width'            => 0.8, // Clickable area size, as percentage of image size: 0.0 - 1.0
            'height'           => 0.6224662, // ...
            'rotation'         => 0.0,
            'is_sticker'       => true, // Don't change this value.
        ],
    ],
];

$client = new GuzzleHttp\Client();
$outputFile = Utils::createTempFile(sys_get_temp_dir(), 'IMG');

try {
    $response = $client->request('GET', $mediaInfo->getImageVersions2()->getCandidates()[0]->getUrl(), ['sink' => $outputFile]);

    // This example will upload the image via our automatic photo processing
    // class. It will ensure that the story file matches the ~9:16 (portrait)
    // aspect ratio needed by Instagram stories.
    $photo = new \InstagramAPI\Media\Photo\InstagramPhoto($outputFile, ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);
    $ig->story->uploadPhoto($photo->getFile(), $metadata);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
} finally {
    @unlink($outputFile);
}
