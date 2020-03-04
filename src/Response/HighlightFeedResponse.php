<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * HighlightFeedResponse.
 *
 * @method bool getAutoLoadMoreEnabled()
 * @method mixed getMessage()
 * @method string getNextMaxId()
 * @method bool getShowEmptyState()
 * @method string getStatus()
 * @method Model\Story[] getStories()
 * @method Model\StoryTray[] getTray()
 * @method Model\StoryTvChannel getTvChannel()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isMessage()
 * @method bool isNextMaxId()
 * @method bool isShowEmptyState()
 * @method bool isStatus()
 * @method bool isStories()
 * @method bool isTray()
 * @method bool isTvChannel()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(bool $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setShowEmptyState(bool $value)
 * @method $this setStatus(string $value)
 * @method $this setStories(Model\Story[] $value)
 * @method $this setTray(Model\StoryTray[] $value)
 * @method $this setTvChannel(Model\StoryTvChannel $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetMessage()
 * @method $this unsetNextMaxId()
 * @method $this unsetShowEmptyState()
 * @method $this unsetStatus()
 * @method $this unsetStories()
 * @method $this unsetTray()
 * @method $this unsetTvChannel()
 * @method $this unset_Messages()
 */
class HighlightFeedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'auto_load_more_enabled' => 'bool',
        'next_max_id'            => 'string',
        'stories'                => 'Model\Story[]',
        'show_empty_state'       => 'bool',
        'tray'                   => 'Model\StoryTray[]',
        'tv_channel'             => 'Model\StoryTvChannel',
    ];
}
