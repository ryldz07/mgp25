<?php

/*
 * IMPORTANT!
 * You need https://github.com/Seldaek/monolog to run this example:
 * $ composer require monolog/monolog
 *
 * Also, if you have a 32-bit PHP build, you have to enable the GMP extension:
 * http://php.net/manual/en/book.gmp.php
 */

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

$loop = \React\EventLoop\Factory::create();
if ($debug) {
    $logger = new \Monolog\Logger('rtc');
    $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::INFO));
} else {
    $logger = null;
}
$rtc = new \InstagramAPI\Realtime($ig, $loop, $logger);
$rtc->on('live-started', function (\InstagramAPI\Realtime\Payload\LiveBroadcast $live) {
    printf('[RTC] Live broadcast %s has been started%s', $live->getBroadcastId(), PHP_EOL);
});
$rtc->on('live-stopped', function (\InstagramAPI\Realtime\Payload\LiveBroadcast $live) {
    printf('[RTC] Live broadcast %s has been stopped%s', $live->getBroadcastId(), PHP_EOL);
});
$rtc->on('direct-story-created', function (\InstagramAPI\Response\Model\DirectThread $thread) {
    printf('[RTC] Story %s has been created%s', $thread->getThreadId(), PHP_EOL);
});
$rtc->on('direct-story-updated', function ($threadId, $threadItemId, \InstagramAPI\Response\Model\DirectThreadItem $threadItem) {
    printf('[RTC] Item %s has been created in story %s%s', $threadItemId, $threadId, PHP_EOL);
});
$rtc->on('direct-story-screenshot', function ($threadId, \InstagramAPI\Realtime\Payload\StoryScreenshot $screenshot) {
    printf('[RTC] %s has taken screenshot of story %s%s', $screenshot->getActionUserDict()->getUsername(), $threadId, PHP_EOL);
});
$rtc->on('direct-story-action', function ($threadId, \InstagramAPI\Response\Model\ActionBadge $storyAction) {
    printf('[RTC] Story in thread %s has badge %s now%s', $threadId, $storyAction->getActionType(), PHP_EOL);
});
$rtc->on('thread-created', function ($threadId, \InstagramAPI\Response\Model\DirectThread $thread) {
    printf('[RTC] Thread %s has been created%s', $threadId, PHP_EOL);
});
$rtc->on('thread-updated', function ($threadId, \InstagramAPI\Response\Model\DirectThread $thread) {
    printf('[RTC] Thread %s has been updated%s', $threadId, PHP_EOL);
});
$rtc->on('thread-notify', function ($threadId, $threadItemId, \InstagramAPI\Realtime\Payload\ThreadAction $notify) {
    printf('[RTC] Thread %s has notification from %s%s', $threadId, $notify->getUserId(), PHP_EOL);
});
$rtc->on('thread-seen', function ($threadId, $userId, \InstagramAPI\Response\Model\DirectThreadLastSeenAt $seenAt) {
    printf('[RTC] Thread %s has been checked by %s%s', $threadId, $userId, PHP_EOL);
});
$rtc->on('thread-activity', function ($threadId, \InstagramAPI\Realtime\Payload\ThreadActivity $activity) {
    printf('[RTC] Thread %s has some activity made by %s%s', $threadId, $activity->getSenderId(), PHP_EOL);
});
$rtc->on('thread-item-created', function ($threadId, $threadItemId, \InstagramAPI\Response\Model\DirectThreadItem $threadItem) {
    printf('[RTC] Item %s has been created in thread %s%s', $threadItemId, $threadId, PHP_EOL);
});
$rtc->on('thread-item-updated', function ($threadId, $threadItemId, \InstagramAPI\Response\Model\DirectThreadItem $threadItem) {
    printf('[RTC] Item %s has been updated in thread %s%s', $threadItemId, $threadId, PHP_EOL);
});
$rtc->on('thread-item-removed', function ($threadId, $threadItemId) {
    printf('[RTC] Item %s has been removed from thread %s%s', $threadItemId, $threadId, PHP_EOL);
});
$rtc->on('client-context-ack', function (\InstagramAPI\Realtime\Payload\Action\AckAction $ack) {
    printf('[RTC] Received ACK for %s with status %s%s', $ack->getPayload()->getClientContext(), $ack->getStatus(), PHP_EOL);
});
$rtc->on('unseen-count-update', function ($inbox, \InstagramAPI\Response\Model\DirectSeenItemPayload $payload) {
    printf('[RTC] Updating unseen count in %s to %d%s', $inbox, $payload->getCount(), PHP_EOL);
});
$rtc->on('presence', function (\InstagramAPI\Response\Model\UserPresence $presence) {
    $action = $presence->getIsActive() ? 'is now using' : 'just closed';
    printf('[RTC] User %s %s one of Instagram apps%s', $presence->getUserId(), $action, PHP_EOL);
});
$rtc->on('error', function (\Exception $e) use ($rtc, $loop) {
    printf('[!!!] Got fatal error from Realtime: %s%s', $e->getMessage(), PHP_EOL);
    $rtc->stop();
    $loop->stop();
});
$rtc->start();

$loop->run();
