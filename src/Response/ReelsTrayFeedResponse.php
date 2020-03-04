<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ReelsTrayFeedResponse.
 *
 * @method Model\Broadcast[] getBroadcasts()
 * @method int getFaceFilterNuxVersion()
 * @method bool getHasNewNuxStory()
 * @method mixed getMessage()
 * @method Model\PostLive getPostLive()
 * @method string getStatus()
 * @method int getStickerVersion()
 * @method bool getStoriesViewerGesturesNuxEligible()
 * @method string getStoryRankingToken()
 * @method Model\TraySuggestions[] getSuggestions()
 * @method Model\StoryTray[] getTray()
 * @method Model\_Message[] get_Messages()
 * @method bool isBroadcasts()
 * @method bool isFaceFilterNuxVersion()
 * @method bool isHasNewNuxStory()
 * @method bool isMessage()
 * @method bool isPostLive()
 * @method bool isStatus()
 * @method bool isStickerVersion()
 * @method bool isStoriesViewerGesturesNuxEligible()
 * @method bool isStoryRankingToken()
 * @method bool isSuggestions()
 * @method bool isTray()
 * @method bool is_Messages()
 * @method $this setBroadcasts(Model\Broadcast[] $value)
 * @method $this setFaceFilterNuxVersion(int $value)
 * @method $this setHasNewNuxStory(bool $value)
 * @method $this setMessage(mixed $value)
 * @method $this setPostLive(Model\PostLive $value)
 * @method $this setStatus(string $value)
 * @method $this setStickerVersion(int $value)
 * @method $this setStoriesViewerGesturesNuxEligible(bool $value)
 * @method $this setStoryRankingToken(string $value)
 * @method $this setSuggestions(Model\TraySuggestions[] $value)
 * @method $this setTray(Model\StoryTray[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetBroadcasts()
 * @method $this unsetFaceFilterNuxVersion()
 * @method $this unsetHasNewNuxStory()
 * @method $this unsetMessage()
 * @method $this unsetPostLive()
 * @method $this unsetStatus()
 * @method $this unsetStickerVersion()
 * @method $this unsetStoriesViewerGesturesNuxEligible()
 * @method $this unsetStoryRankingToken()
 * @method $this unsetSuggestions()
 * @method $this unsetTray()
 * @method $this unset_Messages()
 */
class ReelsTrayFeedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'story_ranking_token'                  => 'string',
        'broadcasts'                           => 'Model\Broadcast[]',
        'tray'                                 => 'Model\StoryTray[]',
        'post_live'                            => 'Model\PostLive',
        'sticker_version'                      => 'int',
        'face_filter_nux_version'              => 'int',
        'stories_viewer_gestures_nux_eligible' => 'bool',
        'has_new_nux_story'                    => 'bool',
        'suggestions'                          => 'Model\TraySuggestions[]',
    ];
}
