<?php

namespace InstagramAPI\Request;

use InstagramAPI\Exception\RequestHeadersTooLargeException;
use InstagramAPI\Exception\ThrottledException;
use InstagramAPI\Response;
use InstagramAPI\Utils;

/**
 * Functions related to finding, exploring and managing relations with people.
 */
class People extends RequestCollection
{
    /**
     * Get details about a specific user via their numerical UserPK ID.
     *
     * NOTE: The real app uses this particular endpoint for _all_ user lookups
     * except "@mentions" (where it uses `getInfoByName()` instead).
     *
     * @param string      $userId Numerical UserPK ID.
     * @param string|null $module From which app module (page) you have opened the profile. One of (incomplete):
     *                            "comment_likers",
     *                            "comment_owner",
     *                            "followers",
     *                            "following",
     *                            "likers_likers_media_view_profile",
     *                            "likers_likers_photo_view_profile",
     *                            "likers_likers_video_view_profile",
     *                            "newsfeed",
     *                            "self_followers",
     *                            "self_following",
     *                            "self_likers_self_likers_media_view_profile",
     *                            "self_likers_self_likers_photo_view_profile",
     *                            "self_likers_self_likers_video_view_profile".
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UserInfoResponse
     */
    public function getInfoById(
        $userId,
        $module = null)
    {
        $request = $this->ig->request("users/{$userId}/info/");
        if ($module !== null) {
            $request->addParam('from_module', $module);
        }

        return $request->getResponse(new Response\UserInfoResponse());
    }

    /**
     * Get details about a specific user via their username.
     *
     * NOTE: The real app only uses this endpoint for profiles opened via "@mentions".
     *
     * @param string $username Username as string (NOT as a numerical ID).
     * @param string $module   From which app module (page) you have opened the profile.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UserInfoResponse
     *
     * @see People::getInfoById() For the list of supported modules.
     */
    public function getInfoByName(
        $username,
        $module = 'feed_timeline')
    {
        return $this->ig->request("users/{$username}/usernameinfo/")
            ->addParam('from_module', $module)
            ->getResponse(new Response\UserInfoResponse());
    }

    /**
     * Get the numerical UserPK ID for a specific user via their username.
     *
     * This is just a convenient helper function. You may prefer to use
     * People::getInfoByName() instead, which lets you see more details.
     *
     * @param string $username Username as string (NOT as a numerical ID).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return string Their numerical UserPK ID.
     *
     * @see People::getInfoByName()
     */
    public function getUserIdForName(
        $username)
    {
        return $this->getInfoByName($username)->getUser()->getPk();
    }

    /**
     * Get user details about your own account.
     *
     * Also try Account::getCurrentUser() instead, for account details.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UserInfoResponse
     *
     * @see Account::getCurrentUser()
     */
    public function getSelfInfo()
    {
        return $this->getInfoById($this->ig->account_id);
    }

    /**
     * Get other people's recent activities related to you and your posts.
     *
     * This feed has information about when people interact with you, such as
     * liking your posts, commenting on your posts, tagging you in photos or in
     * comments, people who started following you, etc.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ActivityNewsResponse
     */
    public function getRecentActivityInbox()
    {
        return $this->ig->request('news/inbox/')
            ->getResponse(new Response\ActivityNewsResponse());
    }

    /**
     * Get news feed with recent activities by accounts you follow.
     *
     * This feed has information about the people you follow, such as what posts
     * they've liked or that they've started following other people.
     *
     * @param string|null $maxId Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FollowingRecentActivityResponse
     */
    public function getFollowingRecentActivity(
        $maxId = null)
    {
        $activity = $this->ig->request('news/');
        if ($maxId !== null) {
            $activity->addParam('max_id', $maxId);
        }

        return $activity->getResponse(new Response\FollowingRecentActivityResponse());
    }

    /**
     * Retrieve bootstrap user data (autocompletion user list).
     *
     * WARNING: This is a special, very heavily throttled API endpoint.
     * Instagram REQUIRES that you wait several minutes between calls to it.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BootstrapUsersResponse|null Will be NULL if throttled by Instagram.
     */
    public function getBootstrapUsers()
    {
        $surfaces = [
            'coefficient_direct_closed_friends_ranking',
            'coefficient_direct_recipients_ranking_variant_2',
            'coefficient_rank_recipient_user_suggestion',
            'coefficient_ios_section_test_bootstrap_ranking',
            'autocomplete_user_list',
        ];

        try {
            $request = $this->ig->request('scores/bootstrap/users/')
                ->addParam('surfaces', json_encode($surfaces));

            return $request->getResponse(new Response\BootstrapUsersResponse());
        } catch (ThrottledException $e) {
            // Throttling is so common that we'll simply return NULL in that case.
            return null;
        }
    }

    /**
     * Show a user's friendship status with you.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipsShowResponse
     */
    public function getFriendship(
        $userId)
    {
        return $this->ig->request("friendships/show/{$userId}/")->getResponse(new Response\FriendshipsShowResponse());
    }

    /**
     * Show multiple users' friendship status with you.
     *
     * @param string|string[] $userList List of numerical UserPK IDs.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipsShowManyResponse
     */
    public function getFriendships(
        $userList)
    {
        if (is_array($userList)) {
            $userList = implode(',', $userList);
        }

        return $this->ig->request('friendships/show_many/')
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('user_ids', $userList)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\FriendshipsShowManyResponse());
    }

    /**
     * Get list of pending friendship requests.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FollowerAndFollowingResponse
     */
    public function getPendingFriendships()
    {
        $request = $this->ig->request('friendships/pending/');

        return $request->getResponse(new Response\FollowerAndFollowingResponse());
    }

    /**
     * Approve a friendship request.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function approveFriendship(
        $userId)
    {
        return $this->ig->request("friendships/approve/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->addPost('radio_type', 'wifi-none')
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Reject a friendship request.
     *
     * Note that the user can simply send you a new request again, after your
     * rejection. If they're harassing you, use People::block() instead.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function rejectFriendship(
        $userId)
    {
        return $this->ig->request("friendships/ignore/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->addPost('radio_type', 'wifi-none')
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Remove one of your followers.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function removeFollower(
        $userId)
    {
        return $this->ig->request("friendships/remove_follower/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->addPost('radio_type', 'wifi-none')
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Mark user over age in order to see sensitive content.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function markUserOverage(
        $userId)
    {
        return $this->ig->request("friendships/mark_user_overage/{$userId}/feed/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Get list of who a user is following.
     *
     * @param string      $userId      Numerical UserPK ID.
     * @param string      $rankToken   The list UUID. You must use the same value for all pages of the list.
     * @param string|null $searchQuery Limit the userlist to ones matching the query.
     * @param string|null $maxId       Next "maximum ID", used for pagination.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FollowerAndFollowingResponse
     *
     * @see Signatures::generateUUID() To create a UUID.
     * @see examples/rankTokenUsage.php For an example.
     */
    public function getFollowing(
        $userId,
        $rankToken,
        $searchQuery = null,
        $maxId = null)
    {
        Utils::throwIfInvalidRankToken($rankToken);
        $request = $this->ig->request("friendships/{$userId}/following/")
            ->addParam('includes_hashtags', true)
            ->addParam('rank_token', $rankToken);
        if ($searchQuery !== null) {
            $request->addParam('query', $searchQuery);
        }
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new Response\FollowerAndFollowingResponse());
    }

    /**
     * Get list of who a user is followed by.
     *
     * @param string      $userId      Numerical UserPK ID.
     * @param string      $rankToken   The list UUID. You must use the same value for all pages of the list.
     * @param string|null $searchQuery Limit the userlist to ones matching the query.
     * @param string|null $maxId       Next "maximum ID", used for pagination.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FollowerAndFollowingResponse
     *
     * @see Signatures::generateUUID() To create a UUID.
     * @see examples/rankTokenUsage.php For an example.
     */
    public function getFollowers(
        $userId,
        $rankToken,
        $searchQuery = null,
        $maxId = null)
    {
        Utils::throwIfInvalidRankToken($rankToken);
        $request = $this->ig->request("friendships/{$userId}/followers/")
            ->addParam('rank_token', $rankToken);
        if ($searchQuery !== null) {
            $request->addParam('query', $searchQuery);
        }
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new Response\FollowerAndFollowingResponse());
    }

    /**
     * Get list of who you are following.
     *
     * @param string      $rankToken   The list UUID. You must use the same value for all pages of the list.
     * @param string|null $searchQuery Limit the userlist to ones matching the query.
     * @param string|null $maxId       Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FollowerAndFollowingResponse
     *
     * @see Signatures::generateUUID() To create a UUID.
     * @see examples/rankTokenUsage.php For an example.
     */
    public function getSelfFollowing(
        $rankToken,
        $searchQuery = null,
        $maxId = null)
    {
        return $this->getFollowing($this->ig->account_id, $rankToken, $searchQuery, $maxId);
    }

    /**
     * Get list of your own followers.
     *
     * @param string      $rankToken   The list UUID. You must use the same value for all pages of the list.
     * @param string|null $searchQuery Limit the userlist to ones matching the query.
     * @param string|null $maxId       Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FollowerAndFollowingResponse
     *
     * @see Signatures::generateUUID() To create a UUID.
     * @see examples/rankTokenUsage.php For an example.
     */
    public function getSelfFollowers(
        $rankToken,
        $searchQuery = null,
        $maxId = null)
    {
        return $this->getFollowers($this->ig->account_id, $rankToken, $searchQuery, $maxId);
    }

    /**
     * Search for Instagram users.
     *
     * @param string         $query       The username or full name to search for.
     * @param string[]|int[] $excludeList Array of numerical user IDs (ie "4021088339")
     *                                    to exclude from the response, allowing you to skip users
     *                                    from a previous call to get more results.
     * @param string|null    $rankToken   A rank token from a first call response.
     *
     * @throws \InvalidArgumentException                  If invalid query or
     *                                                    trying to exclude too
     *                                                    many user IDs.
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SearchUserResponse
     *
     * @see SearchUserResponse::getRankToken() To get a rank token from the response.
     * @see examples/paginateWithExclusion.php For an example.
     */
    public function search(
        $query,
        array $excludeList = [],
        $rankToken = null)
    {
        // Do basic query validation.
        if (!is_string($query) || $query === '') {
            throw new \InvalidArgumentException('Query must be a non-empty string.');
        }

        $request = $this->_paginateWithExclusion(
            $this->ig->request('users/search/')
                ->addParam('q', $query)
                ->addParam('timezone_offset', date('Z')),
            $excludeList,
            $rankToken
        );

        try {
            /** @var Response\SearchUserResponse $result */
            $result = $request->getResponse(new Response\SearchUserResponse());
        } catch (RequestHeadersTooLargeException $e) {
            $result = new Response\SearchUserResponse([
                'has_more'    => false,
                'num_results' => 0,
                'users'       => [],
                'rank_token'  => $rankToken,
            ]);
        }

        return $result;
    }

    /**
     * Get business account details.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\AccountDetailsResponse
     */
    public function getAccountDetails(
        $userId)
    {
        return $this->ig->request("users/{$userId}/account_details/")
            ->getResponse(new Response\AccountDetailsResponse());
    }

    /**
     * Get a business account's former username(s).
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FormerUsernamesResponse
     */
    public function getFormerUsernames(
        $userId)
    {
        return $this->ig->request("users/{$userId}/former_usernames/")
            ->getResponse(new Response\FormerUsernamesResponse());
    }

    /**
     * Get a business account's shared follower base with similar accounts.
     *
     * @param string $userId Numerical UserPk ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SharedFollowersResponse
     */
    public function getSharedFollowers(
        $userId)
    {
        return $this->ig->request("users/{$userId}/shared_follower_accounts/")
            ->getResponse(new Response\SharedFollowersResponse());
    }

    /**
     * Get a business account's active ads on feed.
     *
     * @param string      $targetUserId Numerical UserPk ID.
     * @param string|null $maxId        Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ActiveFeedAdsResponse
     */
    public function getActiveFeedAds(
        $targetUserId,
        $maxId = null)
    {
        return $this->_getActiveAds($targetUserId, '35', $maxId);
    }

    /**
     * Get a business account's active ads on stories.
     *
     * @param string      $targetUserId Numerical UserPk ID.
     * @param string|null $maxId        Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ActiveReelAdsResponse
     */
    public function getActiveStoryAds(
        $targetUserId,
        $maxId = null)
    {
        return $this->_getActiveAds($targetUserId, '49', $maxId);
    }

    /**
     * Helper function for getting active ads for business accounts.
     *
     * @param string      $targetUserId Numerical UserPk ID.
     * @param string      $pageType     Content-type id(?) of the ad. 35 is feed ads and 49 is story ads.
     * @param string|null $maxId        Next "maximum ID", used for pagination.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return Response
     */
    protected function _getActiveAds(
        $targetUserId,
        $pageType,
        $maxId = null)
    {
        $request = $this->ig->request('ads/view_ads/')
            ->setSignedPost(false)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('target_user_id', $targetUserId)
            ->addPost('page_type', $pageType);
        if ($maxId !== null) {
            $request->addPost('next_max_id', $maxId);
        }
        $request->addPost('ig_user_id', $this->ig->account_id);

        switch ($pageType) {
            case '35':
                return $request->getResponse(new Response\ActiveFeedAdsResponse());
                break;
            case '49':
                return $request->getResponse(new Response\ActiveReelAdsResponse());
                break;
            default:
                throw new \InvalidArgumentException('Invalid page type.');
        }
    }

    /**
     * Search for users by linking your address book to Instagram.
     *
     * WARNING: You must unlink your current address book before you can link
     * another one to search again, otherwise you will just keep getting the
     * same response about your currently linked address book every time!
     *
     * @param array  $contacts
     * @param string $module
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LinkAddressBookResponse
     *
     * @see People::unlinkAddressBook()
     */
    public function linkAddressBook(
        array $contacts,
        $module = 'find_friends_contacts')
    {
        return $this->ig->request('address_book/link/')
            ->setIsBodyCompressed(true)
            ->setSignedPost(false)
            ->addPost('phone_id', $this->ig->phone_id)
            ->addPost('module', $module)
            ->addPost('contacts', json_encode($contacts))
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('device_id', $this->ig->device_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\LinkAddressBookResponse());
    }

    /**
     * Unlink your address book from Instagram.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UnlinkAddressBookResponse
     */
    public function unlinkAddressBook()
    {
        return $this->ig->request('address_book/unlink/')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\UnlinkAddressBookResponse());
    }

    /**
     * Discover new people via Facebook's algorithm.
     *
     * This matches you with other people using multiple algorithms such as
     * "friends of friends", "location", "people using similar hashtags", etc.
     *
     * @param string|null $maxId Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DiscoverPeopleResponse
     */
    public function discoverPeople(
        $maxId = null)
    {
        $request = $this->ig->request('discover/ayml/')
            ->setSignedPost(false)
            ->addPost('phone_id', $this->ig->phone_id)
            ->addPost('module', 'discover_people')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('paginate', true);

        if ($maxId !== null) {
            $request->addPost('max_id', $maxId);
        }

        return $request->getResponse(new Response\DiscoverPeopleResponse());
    }

    /**
     * Get suggested users related to a user.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SuggestedUsersResponse
     */
    public function getSuggestedUsers(
        $userId)
    {
        return $this->ig->request('discover/chaining/')
            ->addParam('target_id', $userId)
            ->getResponse(new Response\SuggestedUsersResponse());
    }

    /**
     * Get suggested users via account badge.
     *
     * This is the endpoint for when you press the "user icon with the plus
     * sign" on your own profile in the Instagram app. Its amount of suggestions
     * matches the number on the badge, and it usually only has a handful (1-4).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SuggestedUsersBadgeResponse
     */
    public function getSuggestedUsersBadge()
    {
        return $this->ig->request('discover/profile_su_badge/')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('module', 'discover_people')
            ->getResponse(new Response\SuggestedUsersBadgeResponse());
    }

    /**
     * Hide suggested user, so that they won't be suggested again.
     *
     * You must provide the correct algorithm for the user you want to hide,
     * which can be seen in their "algorithm" value in People::discoverPeople().
     *
     * Here is a probably-outdated list of algorithms and their meanings:
     *
     * - realtime_chaining_algorithm = ?
     * - realtime_chaining_ig_coeff_algorithm = ?
     * - tfidf_city_algorithm = Popular people near you.
     * - hashtag_interest_algorithm = Popular people on similar hashtags as you.
     * - second_order_followers_algorithm = Popular.
     * - super_users_algorithm = Popular.
     * - followers_algorithm = Follows you.
     * - ig_friends_of_friends_from_tao_laser_algorithm = ?
     * - page_rank_algorithm = ?
     *
     * TODO: Do more research about this function and document it properly.
     *
     * @param string $userId    Numerical UserPK ID.
     * @param string $algorithm Which algorithm to hide the suggestion from;
     *                          must match that user's "algorithm" value in
     *                          functions like People::discoverPeople().
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SuggestedUsersResponse
     */
    public function hideSuggestedUser(
        $userId,
        $algorithm)
    {
        return $this->ig->request('discover/aysf_dismiss/')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addParam('target_id', $userId)
            ->addParam('algorithm', $algorithm)
            ->getResponse(new Response\SuggestedUsersResponse());
    }

    /**
     * Follow a user.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function follow(
        $userId)
    {
        return $this->ig->request("friendships/create/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->addPost('radio_type', 'wifi-none')
            ->addPost('device_id', $this->ig->device_id)
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Unfollow a user.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function unfollow(
        $userId)
    {
        return $this->ig->request("friendships/destroy/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->addPost('radio_type', 'wifi-none')
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Enable high priority for a user you are following.
     *
     * When you mark someone as favorite, you will receive app push
     * notifications when that user uploads media, and their shared
     * media will get higher visibility. For instance, their stories
     * will be placed at the front of your reels-tray, and their
     * timeline posts will stay visible for longer on your homescreen.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function favorite(
        $userId)
    {
        return $this->ig->request("friendships/favorite/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Disable high priority for a user you are following.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function unfavorite(
        $userId)
    {
        return $this->ig->request("friendships/unfavorite/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Turn on story notifications.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function favoriteForStories(
        $userId)
    {
        return $this->ig->request("friendships/favorite_for_stories/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Turn off story notifications.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function unfavoriteForStories(
        $userId)
    {
        return $this->ig->request("friendships/unfavorite_for_stories/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Report a user as spam.
     *
     * @param string $userId     Numerical UserPK ID.
     * @param string $sourceName (optional) Source app-module of the report.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function report(
        $userId,
        $sourceName = 'profile')
    {
        return $this->ig->request("users/{$userId}/flag_user/")
            ->addPost('reason_id', 1)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->addPost('source_name', $sourceName)
            ->addPost('is_spam', true)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Block a user.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function block(
        $userId)
    {
        return $this->ig->request("friendships/block/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Mute stories, posts or both from a user.
     *
     * It prevents user media from showing up in the timeline and/or story feed.
     *
     * @param string $userId Numerical UserPK ID.
     * @param string $option Selection of what type of media are going to be muted.
     *                       Available options: 'story', 'post' or 'all'.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function muteUserMedia(
        $userId,
        $option)
    {
        return $this->_muteOrUnmuteUserMedia($userId, $option, 'friendships/mute_posts_or_story_from_follow/');
    }

    /**
     * Unmute stories, posts or both from a user.
     *
     * @param string $userId Numerical UserPK ID.
     * @param string $option Selection of what type of media are going to be muted.
     *                       Available options: 'story', 'post' or 'all'.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function unmuteUserMedia(
        $userId,
        $option)
    {
        return $this->_muteOrUnmuteUserMedia($userId, $option, 'friendships/unmute_posts_or_story_from_follow/');
    }

    /**
     * Helper function to mute user media.
     *
     * @param string $userId   Numerical UserPK ID.
     * @param string $option   Selection of what type of media are going to be muted.
     *                         Available options: 'story', 'post' or 'all'.
     * @param string $endpoint API endpoint for muting/unmuting user media.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     *
     * @see People::muteUserMedia()
     * @see People::unmuteUserMedia()
     */
    protected function _muteOrUnmuteUserMedia(
        $userId,
        $option,
        $endpoint)
    {
        $request = $this->ig->request($endpoint)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken());

        switch ($option) {
            case 'story':
                $request->addPost('target_reel_author_id', $userId);
                break;
            case 'post':
                $request->addPost('target_posts_author_id', $userId);
                break;
            case 'all':
                $request->addPost('target_reel_author_id', $userId);
                $request->addPost('target_posts_author_id', $userId);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid muting option.', $option));
        }

        return $request->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Unblock a user.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     */
    public function unblock(
        $userId)
    {
        return $this->ig->request("friendships/unblock/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_id', $userId)
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Get a list of all blocked users.
     *
     * @param string|null $maxId Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BlockedListResponse
     */
    public function getBlockedList(
        $maxId = null)
    {
        $request = $this->ig->request('users/blocked_list/');
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new Response\BlockedListResponse());
    }

    /**
     * Block a user's ability to see your stories.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     *
     * @see People::muteFriendStory()
     */
    public function blockMyStory(
        $userId)
    {
        return $this->ig->request("friendships/block_friend_reel/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('source', 'profile')
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Unblock a user so that they can see your stories again.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     *
     * @see People::unmuteFriendStory()
     */
    public function unblockMyStory(
        $userId)
    {
        return $this->ig->request("friendships/unblock_friend_reel/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('source', 'profile')
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Get the list of users who are blocked from seeing your stories.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BlockedReelsResponse
     */
    public function getBlockedStoryList()
    {
        return $this->ig->request('friendships/blocked_reels/')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\BlockedReelsResponse());
    }

    /**
     * Mute a friend's stories, so that you no longer see their stories.
     *
     * This hides them from your reels tray (the "latest stories" bar on the
     * homescreen of the app), but it does not block them from seeing *your*
     * stories.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     *
     * @see People::blockMyStory()
     */
    public function muteFriendStory(
        $userId)
    {
        return $this->ig->request("friendships/mute_friend_reel/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Unmute a friend's stories, so that you see their stories again.
     *
     * This does not unblock their ability to see *your* stories.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FriendshipResponse
     *
     * @see People::unblockMyStory()
     */
    public function unmuteFriendStory(
        $userId)
    {
        return $this->ig->request("friendships/unmute_friend_reel/{$userId}/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\FriendshipResponse());
    }

    /**
     * Get the list of users on your close friends list.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CloseFriendsResponse
     */
    public function getCloseFriends()
    {
        return $this->ig->request('friendships/besties/')
            ->getResponse(new Response\CloseFriendsResponse());
    }

    /**
     * Get the list of suggested users for your close friends list.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CloseFriendsResponse
     */
    public function getSuggestedCloseFriends()
    {
        return $this->ig->request('friendships/bestie_suggestions/')
            ->getResponse(new Response\CloseFriendsResponse());
    }

    /**
     * Add or Remove users from your close friends list.
     *
     * Note: You probably shouldn't touch $module and $source as there is only one way to modify your close friends.
     *
     * @param array  $add    Users to add to your close friends list.
     * @param array  $remove Users to remove from your close friends list.
     * @param string $module (optional) From which app module (page) you have change your close friends list.
     * @param string $source (optional) Source page of app-module of where you changed your close friends list.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function setCloseFriends(
        array $add,
        array $remove,
        $module = 'favorites_home_list',
        $source = 'audience_manager')
    {
        return $this->ig->request('friendships/set_besties/')
            ->setSignedPost(true)
            ->addPost('module', $module)
            ->addPost('source', $source)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('remove', $remove)
            ->addPost('add', $add)
            ->getResponse(new Response\GenericResponse());
    }
}
