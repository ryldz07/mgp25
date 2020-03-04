<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * BusinessNode.
 *
 * @method mixed getAllFollowersAgeGraph()
 * @method mixed getAverageEngagementCount()
 * @method mixed getFollowersCount()
 * @method mixed getFollowersDeltaFromLastWeek()
 * @method mixed getFollowersTopCitiesGraph()
 * @method mixed getFollowersUnitState()
 * @method mixed getGenderGraph()
 * @method mixed getLastWeekCall()
 * @method mixed getLastWeekEmail()
 * @method mixed getLastWeekGetDirection()
 * @method mixed getLastWeekImpressions()
 * @method mixed getLastWeekImpressionsDayGraph()
 * @method mixed getLastWeekProfileVisits()
 * @method mixed getLastWeekProfileVisitsDayGraph()
 * @method mixed getLastWeekReach()
 * @method mixed getLastWeekReachDayGraph()
 * @method mixed getLastWeekText()
 * @method mixed getLastWeekWebsiteVisits()
 * @method mixed getPostsCount()
 * @method mixed getPostsDeltaFromLastWeek()
 * @method mixed getState()
 * @method mixed getSummaryPosts()
 * @method mixed getSummaryPromotions()
 * @method mixed getSummaryStories()
 * @method mixed getTodayHourlyGraph()
 * @method mixed getTopPosts()
 * @method mixed getTypename()
 * @method mixed getWeekOverWeekCall()
 * @method mixed getWeekOverWeekEmail()
 * @method mixed getWeekOverWeekGetDirection()
 * @method mixed getWeekOverWeekImpressions()
 * @method mixed getWeekOverWeekProfileVisits()
 * @method mixed getWeekOverWeekReach()
 * @method mixed getWeekOverWeekText()
 * @method mixed getWeekOverWeekWebsiteVisits()
 * @method bool isAllFollowersAgeGraph()
 * @method bool isAverageEngagementCount()
 * @method bool isFollowersCount()
 * @method bool isFollowersDeltaFromLastWeek()
 * @method bool isFollowersTopCitiesGraph()
 * @method bool isFollowersUnitState()
 * @method bool isGenderGraph()
 * @method bool isLastWeekCall()
 * @method bool isLastWeekEmail()
 * @method bool isLastWeekGetDirection()
 * @method bool isLastWeekImpressions()
 * @method bool isLastWeekImpressionsDayGraph()
 * @method bool isLastWeekProfileVisits()
 * @method bool isLastWeekProfileVisitsDayGraph()
 * @method bool isLastWeekReach()
 * @method bool isLastWeekReachDayGraph()
 * @method bool isLastWeekText()
 * @method bool isLastWeekWebsiteVisits()
 * @method bool isPostsCount()
 * @method bool isPostsDeltaFromLastWeek()
 * @method bool isState()
 * @method bool isSummaryPosts()
 * @method bool isSummaryPromotions()
 * @method bool isSummaryStories()
 * @method bool isTodayHourlyGraph()
 * @method bool isTopPosts()
 * @method bool isTypename()
 * @method bool isWeekOverWeekCall()
 * @method bool isWeekOverWeekEmail()
 * @method bool isWeekOverWeekGetDirection()
 * @method bool isWeekOverWeekImpressions()
 * @method bool isWeekOverWeekProfileVisits()
 * @method bool isWeekOverWeekReach()
 * @method bool isWeekOverWeekText()
 * @method bool isWeekOverWeekWebsiteVisits()
 * @method $this setAllFollowersAgeGraph(mixed $value)
 * @method $this setAverageEngagementCount(mixed $value)
 * @method $this setFollowersCount(mixed $value)
 * @method $this setFollowersDeltaFromLastWeek(mixed $value)
 * @method $this setFollowersTopCitiesGraph(mixed $value)
 * @method $this setFollowersUnitState(mixed $value)
 * @method $this setGenderGraph(mixed $value)
 * @method $this setLastWeekCall(mixed $value)
 * @method $this setLastWeekEmail(mixed $value)
 * @method $this setLastWeekGetDirection(mixed $value)
 * @method $this setLastWeekImpressions(mixed $value)
 * @method $this setLastWeekImpressionsDayGraph(mixed $value)
 * @method $this setLastWeekProfileVisits(mixed $value)
 * @method $this setLastWeekProfileVisitsDayGraph(mixed $value)
 * @method $this setLastWeekReach(mixed $value)
 * @method $this setLastWeekReachDayGraph(mixed $value)
 * @method $this setLastWeekText(mixed $value)
 * @method $this setLastWeekWebsiteVisits(mixed $value)
 * @method $this setPostsCount(mixed $value)
 * @method $this setPostsDeltaFromLastWeek(mixed $value)
 * @method $this setState(mixed $value)
 * @method $this setSummaryPosts(mixed $value)
 * @method $this setSummaryPromotions(mixed $value)
 * @method $this setSummaryStories(mixed $value)
 * @method $this setTodayHourlyGraph(mixed $value)
 * @method $this setTopPosts(mixed $value)
 * @method $this setTypename(mixed $value)
 * @method $this setWeekOverWeekCall(mixed $value)
 * @method $this setWeekOverWeekEmail(mixed $value)
 * @method $this setWeekOverWeekGetDirection(mixed $value)
 * @method $this setWeekOverWeekImpressions(mixed $value)
 * @method $this setWeekOverWeekProfileVisits(mixed $value)
 * @method $this setWeekOverWeekReach(mixed $value)
 * @method $this setWeekOverWeekText(mixed $value)
 * @method $this setWeekOverWeekWebsiteVisits(mixed $value)
 * @method $this unsetAllFollowersAgeGraph()
 * @method $this unsetAverageEngagementCount()
 * @method $this unsetFollowersCount()
 * @method $this unsetFollowersDeltaFromLastWeek()
 * @method $this unsetFollowersTopCitiesGraph()
 * @method $this unsetFollowersUnitState()
 * @method $this unsetGenderGraph()
 * @method $this unsetLastWeekCall()
 * @method $this unsetLastWeekEmail()
 * @method $this unsetLastWeekGetDirection()
 * @method $this unsetLastWeekImpressions()
 * @method $this unsetLastWeekImpressionsDayGraph()
 * @method $this unsetLastWeekProfileVisits()
 * @method $this unsetLastWeekProfileVisitsDayGraph()
 * @method $this unsetLastWeekReach()
 * @method $this unsetLastWeekReachDayGraph()
 * @method $this unsetLastWeekText()
 * @method $this unsetLastWeekWebsiteVisits()
 * @method $this unsetPostsCount()
 * @method $this unsetPostsDeltaFromLastWeek()
 * @method $this unsetState()
 * @method $this unsetSummaryPosts()
 * @method $this unsetSummaryPromotions()
 * @method $this unsetSummaryStories()
 * @method $this unsetTodayHourlyGraph()
 * @method $this unsetTopPosts()
 * @method $this unsetTypename()
 * @method $this unsetWeekOverWeekCall()
 * @method $this unsetWeekOverWeekEmail()
 * @method $this unsetWeekOverWeekGetDirection()
 * @method $this unsetWeekOverWeekImpressions()
 * @method $this unsetWeekOverWeekProfileVisits()
 * @method $this unsetWeekOverWeekReach()
 * @method $this unsetWeekOverWeekText()
 * @method $this unsetWeekOverWeekWebsiteVisits()
 */
class BusinessNode extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'typename'                           => '',
        'followers_count'                    => '',
        'followers_delta_from_last_week'     => '',
        'posts_count'                        => '',
        'posts_delta_from_last_week'         => '',
        'last_week_impressions'              => '',
        'week_over_week_impressions'         => '',
        'last_week_reach'                    => '',
        'week_over_week_reach'               => '',
        'last_week_profile_visits'           => '',
        'week_over_week_profile_visits'      => '',
        'last_week_website_visits'           => '',
        'week_over_week_website_visits'      => '',
        'last_week_call'                     => '',
        'week_over_week_call'                => '',
        'last_week_text'                     => '',
        'week_over_week_text'                => '',
        'last_week_email'                    => '',
        'week_over_week_email'               => '',
        'last_week_get_direction'            => '',
        'week_over_week_get_direction'       => '',
        'average_engagement_count'           => '',
        'last_week_impressions_day_graph'    => '',
        'last_week_reach_day_graph'          => '',
        'last_week_profile_visits_day_graph' => '',
        'summary_posts'                      => '',
        'state'                              => '',
        'summary_stories'                    => '',
        'followers_unit_state'               => '',
        'today_hourly_graph'                 => '',
        'gender_graph'                       => '',
        'all_followers_age_graph'            => '',
        'followers_top_cities_graph'         => '',
        'summary_promotions'                 => '',
        'top_posts'                          => '',
    ];
}
