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
$videoFilename = '';
//////////////////////

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}

try {
    // NOTE: This code will create a broadcast, which will give us an RTMP url
    // where we are supposed to stream-upload the media we want to broadcast.
    //
    // The following code is using FFMPEG to broadcast, although other
    // alternatives are valid too, like OBS (Open Broadcaster Software,
    // https://obsproject.com).
    //
    // For more information on FFMPEG, see:
    // https://github.com/mgp25/Instagram-API/issues/1488#issuecomment-324271177
    // and for OBS, see:
    // https://github.com/mgp25/Instagram-API/issues/1488#issuecomment-333365636

    // Get FFmpeg handler and ensure that the application exists on this system.
    // NOTE: You can supply custom path to the ffmpeg binary, or just leave NULL
    // to autodetect it.
    $ffmpegPath = null;
    $ffmpeg = \InstagramAPI\Media\Video\FFmpeg::factory($ffmpegPath);

    // Tell Instagram that we want to perform a livestream.
    $stream = $ig->live->create();
    $broadcastId = $stream->getBroadcastId();
    $ig->live->start($broadcastId);

    $streamUploadUrl = $stream->getUploadUrl();

    // Broadcast the entire video file.
    // NOTE: The video is broadcasted asynchronously (in the background).
    $broadcastProcess = $ffmpeg->runAsync(sprintf(
        '-rtbufsize 256M -re -i %s -acodec libmp3lame -ar 44100 -b:a 128k -pix_fmt yuv420p -profile:v baseline -s 720x1280 -bufsize 6000k -vb 400k -maxrate 1500k -deinterlace -vcodec libx264 -preset veryfast -g 30 -r 30 -f flv %s',
        \Winbox\Args::escape($videoFilename),
        \Winbox\Args::escape($streamUploadUrl)
    ));

    // The following loop performs important requests to obtain information
    // about the broadcast while it is ongoing.
    // NOTE: This is REQUIRED if you want the comments and likes to appear
    // in your saved post-live feed.
    // NOTE: These requests are sent *while* the video is being broadcasted.
    $lastCommentTs = 0;
    $lastLikeTs = 0;
    do {
        // Get broadcast comments.
        // - The latest comment timestamp will be required for the next
        //   getComments() request.
        // - There are two types of comments: System comments and user comments.
        //   We compare both and keep the newest (most recent) timestamp.
        $commentsResponse = $ig->live->getComments($broadcastId, $lastCommentTs);
        $systemComments = $commentsResponse->getSystemComments();
        $comments = $commentsResponse->getComments();
        if (!empty($systemComments)) {
            $lastCommentTs = $systemComments[0]->getCreatedAt();
        }
        if (!empty($comments) && $comments[0]->getCreatedAt() > $lastCommentTs) {
            $lastCommentTs = $comments[0]->getCreatedAt();
        }

        // Get broadcast heartbeat and viewer count.
        $heartbeatResponse = $ig->live->getHeartbeatAndViewerCount($broadcastId);

        // Check to see if the livestream has been flagged for a policy violation.
        if ($heartbeatResponse->isIsPolicyViolation() && (int) $heartbeatResponse->getIsPolicyViolation() === 1) {
            echo 'Instagram has flagged your content as a policy violation with the following reason: '.($heartbeatResponse->getPolicyViolationReason() == null ? 'Unknown' : $heartbeatResponse->getPolicyViolationReason())."\n";
            // Change this to false if disagree with the policy violation and would would like to continue streaming.
            // - Note: In this example, the violation is always accepted.
            //   In your use case, you may want to prompt the user if
            //   they would like to accept or refute the policy violation.
            if (true) {
                // Get the final viewer list of the broadcast.
                $ig->live->getFinalViewerList($broadcastId);
                // End the broadcast stream while acknowledging the copyright warning given.
                $ig->live->end($broadcastId, true);
                exit(0);
            }
            // Acknowledges the copyright warning and allows you to continue streaming.
            // - Note: This may allow the copyright holder to view your livestream
            //   regardless of your account privacy or if you archive it.
            $ig->live->resumeBroadcastAfterContentMatch($broadcastId);
        }

        // Get broadcast like count.
        // - The latest like timestamp will be required for the next
        //   getLikeCount() request.
        $likeCountResponse = $ig->live->getLikeCount($broadcastId, $lastLikeTs);
        $lastLikeTs = $likeCountResponse->getLikeTs();

        // Get the join request counts.
        // - This doesn't add support for live-with-friends. Rather,
        //   this is only here to emulate the app's livestream flow.
        $ig->live->getJoinRequestCounts($broadcastId);

        sleep(2);
    } while ($broadcastProcess->isRunning());

    // Get the final viewer list of the broadcast.
    // NOTE: You should only use this after the broadcast has stopped uploading.
    $ig->live->getFinalViewerList($broadcastId);

    // End the broadcast stream.
    // NOTE: Instagram will ALSO end the stream if your broadcasting software
    // itself sends a RTMP signal to end the stream. FFmpeg doesn't do that
    // (without patching), but OBS sends such a packet. So be aware of that.
    $ig->live->end($broadcastId);

    // Once the broadcast has ended, you can optionally add the finished
    // broadcast to your post-live feed (saved replay).
    $ig->live->addToPostLive($broadcastId);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}
