<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TagFeedResponse.
 *
 * @method bool getAutoLoadMoreEnabled()
 * @method Model\Item[] getItems()
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method string getNextMaxId()
 * @method mixed getNextMediaIds()
 * @method int getNextPage()
 * @method int getNumResults()
 * @method Model\Item[] getRankedItems()
 * @method Model\Section[] getSections()
 * @method string getStatus()
 * @method Model\StoryTray getStory()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNextMediaIds()
 * @method bool isNextPage()
 * @method bool isNumResults()
 * @method bool isRankedItems()
 * @method bool isSections()
 * @method bool isStatus()
 * @method bool isStory()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(bool $value)
 * @method $this setItems(Model\Item[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNextMediaIds(mixed $value)
 * @method $this setNextPage(int $value)
 * @method $this setNumResults(int $value)
 * @method $this setRankedItems(Model\Item[] $value)
 * @method $this setSections(Model\Section[] $value)
 * @method $this setStatus(string $value)
 * @method $this setStory(Model\StoryTray $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetItems()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetNextMediaIds()
 * @method $this unsetNextPage()
 * @method $this unsetNumResults()
 * @method $this unsetRankedItems()
 * @method $this unsetSections()
 * @method $this unsetStatus()
 * @method $this unsetStory()
 * @method $this unset_Messages()
 */
class TagFeedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'sections'               => 'Model\Section[]',
        'num_results'            => 'int',
        'ranked_items'           => 'Model\Item[]',
        'auto_load_more_enabled' => 'bool',
        'items'                  => 'Model\Item[]',
        'story'                  => 'Model\StoryTray',
        'more_available'         => 'bool',
        'next_max_id'            => 'string',
        'next_media_ids'         => '',
        'next_page'              => 'int',
    ];
}
