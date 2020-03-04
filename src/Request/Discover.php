<?php

namespace InstagramAPI\Request;

use InstagramAPI\Constants;
use InstagramAPI\Exception\RequestHeadersTooLargeException;
use InstagramAPI\Response;

/**
 * General content discovery functions which don't fit into any better groups.
 */
class Discover extends RequestCollection
{
    /**
     * Get Explore tab feed.
     *
     * @param string|null $maxId      Next "maximum ID", used for pagination.
     * @param bool        $isPrefetch Whether this is the first fetch; we'll ignore maxId if TRUE.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ExploreResponse
     */
    public function getExploreFeed(
        $maxId = null,
        $isPrefetch = false)
    {
        $request = $this->ig->request('discover/explore/')
            ->addParam('is_prefetch', $isPrefetch)
            ->addParam('is_from_promote', false)
            ->addParam('timezone_offset', date('Z'))
            ->addParam('session_id', $this->ig->session_id)
            ->addParam('supported_capabilities_new', json_encode(Constants::SUPPORTED_CAPABILITIES));

        if (!$isPrefetch) {
            if ($maxId === null) {
                $maxId = 0;
            }
            $request->addParam('max_id', $maxId);
            $request->addParam('module', 'explore_popular');
        }

        return $request->getResponse(new Response\ExploreResponse());
    }

    /**
     * Report media in the Explore-feed.
     *
     * @param string $exploreSourceToken Token related to the Explore media.
     * @param string $userId             Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ReportExploreMediaResponse
     */
    public function reportExploreMedia(
        $exploreSourceToken,
        $userId)
    {
        return $this->ig->request('discover/explore_report/')
            ->addParam('explore_source_token', $exploreSourceToken)
            ->addParam('m_pk', $this->ig->account_id)
            ->addParam('a_pk', $userId)
            ->getResponse(new Response\ReportExploreMediaResponse());
    }

    /**
     * Search for Instagram users, hashtags and places via Facebook's algorithm.
     *
     * This performs a combined search for "top results" in all 3 areas at once.
     *
     * @param string      $query       The username/full name, hashtag or location to search for.
     * @param string      $latitude    (optional) Latitude.
     * @param string      $longitude   (optional) Longitude.
     * @param array       $excludeList Array of grouped numerical entity IDs (ie "users" => ["4021088339"])
     *                                 to exclude from the response, allowing you to skip entities
     *                                 from a previous call to get more results. The following entities are supported:
     *                                 "users", "places", "tags".
     * @param string|null $rankToken   (When paginating) The rank token from the previous page's response.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FBSearchResponse
     *
     * @see FBSearchResponse::getRankToken() To get a rank token from the response.
     * @see examples/paginateWithExclusion.php For a rank token example (but with a different type of exclude list).
     */
    public function search(
        $query,
        $latitude = null,
        $longitude = null,
        array $excludeList = [],
        $rankToken = null)
    {
        // Do basic query validation.
        if (!is_string($query) || $query === '') {
            throw new \InvalidArgumentException('Query must be a non-empty string.');
        }
        $request = $this->_paginateWithMultiExclusion(
            $this->ig->request('fbsearch/topsearch_flat/')
                ->addParam('context', 'blended')
                ->addParam('query', $query)
                ->addParam('timezone_offset', date('Z')),
            $excludeList,
            $rankToken
        );

        if ($latitude !== null && $longitude !== null) {
            $request
                ->addParam('lat', $latitude)
                ->addParam('lng', $longitude);
        }

        try {
            /** @var Response\FBSearchResponse $result */
            $result = $request->getResponse(new Response\FBSearchResponse());
        } catch (RequestHeadersTooLargeException $e) {
            $result = new Response\FBSearchResponse([
                'has_more'   => false,
                'hashtags'   => [],
                'users'      => [],
                'places'     => [],
                'rank_token' => $rankToken,
            ]);
        }

        return $result;
    }

    /**
     * Get search suggestions via Facebook's algorithm.
     *
     * NOTE: In the app, they're listed as the "Suggested" in the "Top" tab at the "Search" screen.
     *
     * @param string $type One of: "blended", "users", "hashtags" or "places".
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SuggestedSearchesResponse
     */
    public function getSuggestedSearches(
        $type)
    {
        if (!in_array($type, ['blended', 'users', 'hashtags', 'places'], true)) {
            throw new \InvalidArgumentException(sprintf('Unknown search type: %s.', $type));
        }

        return $this->ig->request('fbsearch/suggested_searches/')
            ->addParam('type', $type)
            ->getResponse(new Response\SuggestedSearchesResponse());
    }

    /**
     * Get recent searches via Facebook's algorithm.
     *
     * NOTE: In the app, they're listed as the "Recent" in the "Top" tab at the "Search" screen.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\RecentSearchesResponse
     */
    public function getRecentSearches()
    {
        return $this->ig->request('fbsearch/recent_searches/')
            ->getResponse(new Response\RecentSearchesResponse());
    }

    /**
     * Clear the search history.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function clearSearchHistory()
    {
        return $this->ig->request('fbsearch/clear_search_history/')
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }
}
