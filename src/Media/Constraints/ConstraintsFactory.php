<?php

namespace InstagramAPI\Media\Constraints;

use InstagramAPI\Constants;
use InstagramAPI\Media\ConstraintsInterface;

class ConstraintsFactory
{
    /**
     * Create a constraints set for a given target.
     *
     * @param int $targetFeed one of the FEED_X constants.
     *
     * @return ConstraintsInterface
     *
     * @see Constants
     */
    public static function createFor(
        $targetFeed)
    {
        switch ($targetFeed) {
            case Constants::FEED_STORY:
                $result = new StoryConstraints();
                break;
            case Constants::FEED_DIRECT:
                $result = new DirectConstraints();
                break;
            case Constants::FEED_DIRECT_STORY:
                $result = new DirectStoryConstraints();
                break;
            case Constants::FEED_TV:
                $result = new TvConstraints();
                break;
            case Constants::FEED_TIMELINE_ALBUM:
                $result = new AlbumConstraints();
                break;
            case Constants::FEED_TIMELINE:
            default:
                $result = new TimelineConstraints();
        }

        return $result;
    }
}
