<?php

/*
 * This script was hastily put together so that we can test devices and verify
 * that they're capable of receiving HD videos in Instagram API replies.
 *
 * Why? Because Instagram uses the device info to determine whether to give our
 * "device" the max-resolution video URLs in API replies, or only lower video
 * qualities. Note that User-Agent has ZERO effect on photo resolutions. Only
 * video API replies are affected by the device identifier.
 *
 * The field that actually matters is the one after the manufacturer name, which
 * is the device's model name. That's what affects their API response.
 *
 * Example of a bad model: "Lenovo; S820_AMX_ROW" (they check the latter field).
 */

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

use InstagramAPI\Constants;
use InstagramAPI\Devices\Device;
use InstagramAPI\Devices\DeviceInterface;
use InstagramAPI\Devices\GoodDevices;

$debug = false;
$username = '';
$password = '';

$ig = new \InstagramAPI\Instagram(
    $debug,
    false // Use non-truncated API debugging.
);

$ig->login($username, $password);

// Code for building video lists (uncomment both lines).
// $userPk = $ig->people->getInfoByName('selenagomez')->getUser()->getPk();
// buildVideoList($ig, $userPk); exit;

// List of good videos and resolutions that we MUST see with a GOOD user agent.
// Manually created via buildVideoList() and the code above, by selecting some
// high resolution videos from random profiles. If these are deleted in the
// future or if Instagram adds support for wider than 640px videos, we'll need
// to rebuild this test array with other test videos.
$highResVideoIDs = [
    '1451670806714625231_460563723' => [ // Selena Gomez portrait video.
        ['width'=>640, 'height'=>799, 'url'=>'https://instagram.com/p/BQlXuhNB6zP/'],
        ['width'=> 480, 'height'=>599, 'url'=>'https://instagram.com/p/BQlXuhNB6zP/'],
        ['width'=> 480, 'height'=>599, 'url'=>'https://instagram.com/p/BQlXuhNB6zP/'],
    ],
    '1264771449117881030_460563723' => [ // Selena Gomez 16:9 landscape video.
        ['width'=>640, 'height'=>360, 'url'=>'https://instagram.com/p/BGNXu6SujLG/'],
        ['width'=> 480, 'height'=>270, 'url'=>'https://instagram.com/p/BGNXu6SujLG/'],
        ['width'=> 480, 'height'=>270, 'url'=>'https://instagram.com/p/BGNXu6SujLG/'],
    ],
    '1435631390916900349_460563723' => [ // Selena Gomez max res square video.
        ['width'=>640, 'height'=>640, 'url'=>'https://instagram.com/p/BPsYyTMAHX9/'],
        ['width'=> 480, 'height'=>480, 'url'=>'https://instagram.com/p/BPsYyTMAHX9/'],
        ['width'=> 480, 'height'=>480, 'url'=>'https://instagram.com/p/BPsYyTMAHX9/'],
    ],
];

$testDevices = [];

// Test all of our current, "good" devices too? We must do that periodically.
$testGoodDevices = true;
if ($testGoodDevices) {
    foreach (GoodDevices::getAllGoodDevices() as $deviceString) {
        $testDevices[] = $deviceString;
    }
}

// Additional devices to test. Add these new devices in devicestring format.
$testDevices = array_merge(
    $testDevices,
    [
        // DEMO STRING: This is a low-res device which is NOT capable of
        // getting HD video URLs in API replies. It's just here for developer
        // demo purposes. Also note that it's actually caught by the Device()
        // class' REFUSAL to construct from anything lower than 1920x1080
        // devices, so we had to FAKE its resolution HERE just to get it to
        // even be testable! Its real User Agent string is supposed to be:
        //'17/4.2.2; 240dpi; 480x800; samsung; SM-G350; cs02; hawaii_ss_cs02',
        // However, Instagram only checks the model identifier, in this case
        // "SM-G350", and the test shows that this bad device lacks HD videos.
        '17/4.2.2; 240dpi; 1080x1920; samsung; SM-G350; cs02; hawaii_ss_cs02',
    ]
);

// Build all device objects before we even run the tests, just to catch
// any "bad devicestring" construction errors before wasting time.
foreach ($testDevices as $key => $deviceString) {
    // Create the new Device object, without automatic fallbacks.
    $testDevices[$key] = new Device(Constants::IG_VERSION, Constants::VERSION_CODE, Constants::USER_AGENT_LOCALE, $deviceString, false);
}

// Test all devices in our list!
foreach ($testDevices as $thisDevice) {
    switchDevice($ig, $thisDevice);

    $currentAgent = $ig->device->getUserAgent();
    echo "\n[{$currentAgent}]\n";

    $isBadDevice = false;
    foreach ($highResVideoIDs as $videoId => $expectedResolutions) {
        $bestWidth = $expectedResolutions[0]['width'];
        $bestHeight = $expectedResolutions[0]['height'];
        echo "* Checking {$videoId} (should be: {$bestWidth}x{$bestHeight})...";

        // Retrieve video info (with 4 total attempts in case of throttling).
        for ($attempt = 1; $attempt <= 4; ++$attempt) {
            try {
                $mediaInfo = $ig->media->getInfo($videoId)->getItems()[0];
                break;
            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                if ($attempt < 4) {
                    sleep(10);
                } else {
                    throw $e;
                }
            }
        }

        // Verify all video candidates.
        $videoVersions = $mediaInfo->getVideoVersions();
        if (count($videoVersions) !== count($expectedResolutions)) {
            die('Wrong number of video candidate URLs for media item: '.$videoId);
        }
        $isBadVersions = false;
        foreach ($videoVersions as $idx => $version) {
            $thisWidth = $version->getWidth();
            $thisHeight = $version->getHeight();
            $expectedWidth = $expectedResolutions[$idx]['width'];
            $expectedHeight = $expectedResolutions[$idx]['height'];
            if ($thisWidth != $expectedWidth || $thisHeight != $expectedHeight) {
                $isBadVersions = true;
                break;
            }
        }
        echo $isBadVersions ? "Bad response: {$thisWidth}x{$thisHeight}.\n" : "OK.\n";
        if ($isBadVersions) {
            $isBadDevice = true;
        }
    }

    echo $isBadDevice ? "THIS DEVICE IS BAD AND DOES NOT SUPPORT HD VIDEOS!\n" : "DEVICE OK.\n";
}

// INTERNAL FUNCTIONS...

/**
 * Changes the user-agent sent by the InstagramAPI library.
 *
 * @param \InstagramAPI\Instagram $ig
 * @param DeviceInterface|string  $value
 */
function switchDevice(
    $ig,
    $value)
{
    // Create the new Device object, without automatic fallbacks.
    $device = ($value instanceof DeviceInterface ? $value : new Device(Constants::IG_VERSION, Constants::VERSION_CODE, Constants::USER_AGENT_LOCALE, $value, false));

    // Update the Instagram Client's User-Agent to the new Device.
    $ig->device = $device;
    $ig->client->updateFromCurrentSettings();
}

/**
 * Checks a timeline and builds a list of all videos.
 *
 * Used for building the internal $highResVideoIDs test array.
 *
 * Instagram forces videos (timelines and stories) to be no wider than 640px.
 *
 * Max square video: 640x640. Nothing can ever be bigger than that if square.
 *
 * Max landscape video: 640xDEPENDS ON PHONE (for example 640x360 (16:9 videos)).
 *   In landscape videos, height is always < 640.
 *
 * Max portrait video: 640xDEPENDS ON PHONE (for example 640x799, 640x1136 (16:9)).
 *   In portrait, height IS > 640 (anything less would be a square/landscape).
 *
 * So Instagram lets people post HD resolutions in PORTRAIT, but only SD videos
 * in landscape... This actually leads to a funny realization: To post HD videos
 * with a high HD resolution, you must rotate them so they're always portrait.
 *
 * Also note that Instagram allows UPLOADING videos up to 1080 pixels WIDE. But
 * they will RESIZE them to no more than 640 pixels WIDE. Perhaps they will
 * someday allow 1080-width playback, in which case we will have to test and
 * revise all device identifiers again to make sure they all see the best URLs.
 *
 * @param \InstagramAPI\Instagram $ig
 * @param string                  $userPk
 */
function buildVideoList(
    $ig,
    $userPk)
{
    // We must use a good device to get answers when scanning for HD videos.
    switchDevice($ig, GoodDevices::getRandomGoodDevice());

    echo "[\n";
    $maxId = null;
    do {
        $feed = $ig->timeline->getUserFeed($userPk, $maxId);
        foreach ($feed->getItems() as $item) {
            if ($item->getMediaType() != \InstagramAPI\Response\Model\Item::VIDEO) {
                continue;
            }

            $code = \InstagramAPI\InstagramID::toCode($item->getPk());
            echo sprintf("    '%s' => [\n", $item->getId());
            $videoVersions = $item->getVideoVersions();
            foreach ($videoVersions as $version) {
                echo sprintf(
                    "        ['width'=>%d, 'height'=>%d, 'url'=>'https://instagram.com/p/%s/'],\n",
                    $version->getWidth(), $version->getHeight(), $code
                );
            }
            echo "    ],\n";
        }
        $maxId = $feed->getNextMaxId();
    } while ($maxId !== null);
    echo "]\n";
}
