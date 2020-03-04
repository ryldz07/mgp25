<?php

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

use InstagramAPI\Media\InstagramMedia;
use InstagramAPI\Media\Photo\InstagramPhoto;

/*
 * This script ensures that InstagramPhoto works properly, by testing all
 * of the 8 different JPEG EXIF orientations and ensuring they all correctly
 * produce identical results regardless of pixel orientation/flipping flags.
 *
 * The resulting files must be manually reviewed for visual correctness.
 */

function runTest(
    $inputFile,
    $outputFile,
    $options)
{
    printf("* Processing: '%s'. Output: '%s'.\n", $inputFile, $outputFile);
    $photo = new InstagramPhoto($inputFile, $options);
    if (!copy($photo->getFile(), $outputFile)) {
        throw new \RuntimeException(sprintf('Failed to write to "%s".', $outputFile));
    }
}

// Ensure that we have the JPEG orientation test images.
$testImageFolder = __DIR__.'/exif-orientation-examples/';
if (!is_dir($testImageFolder)) {
    echo "* Downloading test images:\n";
    exec('git clone https://github.com/recurser/exif-orientation-examples.git '.escapeshellarg($testImageFolder));
}

// Process all of the test images.
$files = [];
foreach (['Landscape', 'Portrait'] as $orientation) {
    for ($imgNum = 1; $imgNum <= 8; ++$imgNum) {
        $fileName = sprintf('%s_%d.jpg', $orientation, $imgNum);
        $inputFile = sprintf('%s/%s', $testImageFolder, $fileName);
        if (!is_file($inputFile)) {
            die('Error: Missing "'.$inputFile.'".');
        }

        // Run the cropping test...
        $outputFile = sprintf('%s/result_crop_%s', $testImageFolder, $fileName);
        runTest($inputFile, $outputFile, [
            'forceAspectRatio' => 1.0, // Square.
            'horCropFocus'     => -35, // Always use the same focus, to look for orientation errors.
            'verCropFocus'     => -35, // This combo aims at the upper left corner.
            'operation'        => InstagramMedia::CROP,
        ]);

        // Run the expansion test...
        $outputFile = sprintf('%s/result_expand_%s', $testImageFolder, $fileName);
        runTest($inputFile, $outputFile, [
            'forceAspectRatio' => 1.0, // Square.
            'operation'        => InstagramMedia::EXPAND,
        ]);
    }
}

echo "\n\nAll images have been processed. Manually review the results to ensure they're all correctly processed.\n";
