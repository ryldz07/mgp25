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

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}

/*
 * The code below demonstrates how to get the full server response from an
 * InstagramException object (that's the ONLY type of exception which can
 * have an Instagram server response attached to it).
 *
 * All exceptions thrown by our library are derived from InstagramException.
 * And MOST of them (but NOT ALL) will have a server response object. So it's
 * important that you check hasResponse() before trying to use the response.
 */

// Example 1: Catching EVERYTHING derived from InstagramException so that you
// can look at the server response (IF one is available).

try {
    $ig->media->delete('123456'); // Invalid media ID, to trigger a server error.
} catch (\InstagramAPI\Exception\InstagramException $e) {
    echo 'Something went wrong (InstagramException): '.$e->getMessage()."\n";

    if ($e->hasResponse()) { // <-- VERY IMPORTANT TO CHECK FIRST!
        echo "The current exception contains a full server response:\n";
        $e->getResponse()->printJson();
        echo "Showing the 'did_delete' and 'message' values of the response:\n";
        var_dump($e->getResponse()->getDidDelete());
        var_dump($e->getResponse()->getMessage());
    }
}

// Example 2: Catching the SPECIFIC exception you want (which must be derived
// from InstagramException, otherwise it won't have hasResponse()/getResponse()).

try {
    $ig->media->delete('123456'); // Invalid media ID, to trigger a server error.
} catch (\InstagramAPI\Exception\EndpointException $e) {
    echo 'Something went wrong (EndpointException): '.$e->getMessage()."\n";

    if ($e->hasResponse()) { // <-- VERY IMPORTANT TO CHECK FIRST!
        echo "The current exception contains a full server response:\n";
        $e->getResponse()->printJson();
        echo "Showing the 'did_delete' and 'message' values of the response:\n";
        var_dump($e->getResponse()->getDidDelete());
        var_dump($e->getResponse()->getMessage());
    }

    // Bonus: This shows how to look for specific "error reason" messages in an
    // EndpointException. There are hundreds or even thousands of different
    // error messages and we won't add exceptions for them (that would be a
    // nightmare to maintain). Instead, it's up to the library users to check
    // for the per-endpoint error messages they care about. Such as this one:
    if (strpos($e->getMessage(), 'Could not delete') !== false) {
        echo "* The problem in this generic EndpointException was that Instagram could not delete the media!\n";
    }
}

// With this knowledge, you can now go out and catch the exact exceptions you
// want, and look at specific server details in the reply whenever necessary!
// Note that the easiest way to handle the generic EndpointException (which is
// triggered when any of the generic functions fail) is to do a string search in
// $e->getMessage() to look for a specific error message to know what type of
// error it was. And if you need more details about the error, you can THEN look
// at $e->getResponse() to see the contents of the actual server reply.
