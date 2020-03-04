<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * TimelineFeedResponse.
 *
 * @method bool getAutoLoadMoreEnabled()
 * @method bool getClientFeedChangelistApplied()
 * @method mixed getClientGapEnforcerMatrix()
 * @method string getClientSessionId()
 * @method Model\FeedItem[] getFeedItems()
 * @method string getFeedPillText()
 * @method bool getIsDirectV2Enabled()
 * @method Model\FeedAysf getMegaphone()
 * @method mixed getMessage()
 * @method bool getMoreAvailable()
 * @method string getNextMaxId()
 * @method int getNumResults()
 * @method mixed getPaginationInfo()
 * @method string getStatus()
 * @method string getViewStateVersion()
 * @method Model\_Message[] get_Messages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isClientFeedChangelistApplied()
 * @method bool isClientGapEnforcerMatrix()
 * @method bool isClientSessionId()
 * @method bool isFeedItems()
 * @method bool isFeedPillText()
 * @method bool isIsDirectV2Enabled()
 * @method bool isMegaphone()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isPaginationInfo()
 * @method bool isStatus()
 * @method bool isViewStateVersion()
 * @method bool is_Messages()
 * @method $this setAutoLoadMoreEnabled(bool $value)
 * @method $this setClientFeedChangelistApplied(bool $value)
 * @method $this setClientGapEnforcerMatrix(mixed $value)
 * @method $this setClientSessionId(string $value)
 * @method $this setFeedItems(Model\FeedItem[] $value)
 * @method $this setFeedPillText(string $value)
 * @method $this setIsDirectV2Enabled(bool $value)
 * @method $this setMegaphone(Model\FeedAysf $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(bool $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNumResults(int $value)
 * @method $this setPaginationInfo(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setViewStateVersion(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetClientFeedChangelistApplied()
 * @method $this unsetClientGapEnforcerMatrix()
 * @method $this unsetClientSessionId()
 * @method $this unsetFeedItems()
 * @method $this unsetFeedPillText()
 * @method $this unsetIsDirectV2Enabled()
 * @method $this unsetMegaphone()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetNumResults()
 * @method $this unsetPaginationInfo()
 * @method $this unsetStatus()
 * @method $this unsetViewStateVersion()
 * @method $this unset_Messages()
 */
class TimelineFeedResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'num_results'                    => 'int',
        'client_gap_enforcer_matrix'     => '',
        'is_direct_v2_enabled'           => 'bool',
        'auto_load_more_enabled'         => 'bool',
        'more_available'                 => 'bool',
        'next_max_id'                    => 'string',
        'pagination_info'                => '',
        'feed_items'                     => 'Model\FeedItem[]',
        'megaphone'                      => 'Model\FeedAysf',
        'client_feed_changelist_applied' => 'bool',
        'view_state_version'             => 'string',
        'feed_pill_text'                 => 'string',
        'client_session_id'              => 'string',
    ];
}
