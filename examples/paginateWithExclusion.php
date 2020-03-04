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

try {
    // Let's list locations that match "milan".
    $query = 'milan';

    // Initialize the state.
    $rankToken = null;
    $excludeList = [];
    do {
        // Request the page.
        $response = $ig->location->findPlaces($query, $excludeList, $rankToken);

        // In this example we're simply printing the IDs of this page's items.
        foreach ($response->getItems() as $item) {
            $location = $item->getLocation();
            // Add the item ID to the exclusion list, to tell Instagram's server
            // to skip that item on the next pagination request.
            $excludeList[] = $location->getFacebookPlacesId();
            // Let's print some details about the item.
            printf("%s (%.3f, %.3f)\n", $item->getTitle(), $location->getLat(), $location->getLng());
        }

        // Now we must update the rankToken variable.
        $rankToken = $response->getRankToken();

        // Sleep for 5 seconds before requesting the next page. This is just an
        // example of an okay sleep time. It is very important that your scripts
        // always pause between requests that may run very rapidly, otherwise
        // Instagram will throttle you temporarily for abusing their API!
        echo "Sleeping for 5s...\n";
        sleep(5);
    } while ($response->getHasMore());
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}
