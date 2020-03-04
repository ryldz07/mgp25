<?php

namespace InstagramAPI\Push;

use Fbns\Client\Json;
use InstagramAPI\Push\Payload\BadgeCount;

class Notification
{
    /**
     * @var string
     */
    protected $_json;

    /**
     * @var string
     */
    protected $_title;
    /**
     * @var string
     */
    protected $_message;
    /**
     * @var string
     */
    protected $_tickerText;
    /**
     * @var string
     */
    protected $_igAction;
    /**
     * @var string
     */
    protected $_igActionOverride;
    /**
     * @var string
     */
    protected $_optionalImage;
    /**
     * @var string
     */
    protected $_optionalAvatarUrl;
    /**
     * @var string
     */
    protected $_collapseKey;
    /**
     * @var string
     */
    protected $_sound;
    /**
     * @var string
     */
    protected $_pushId;
    /**
     * @var string
     */
    protected $_pushCategory;
    /**
     * @var string
     */
    protected $_intendedRecipientUserId;
    /**
     * @var string
     */
    protected $_sourceUserId;
    /**
     * @var BadgeCount
     */
    protected $_badgeCount;
    /**
     * @var string
     */
    protected $_inAppActors;

    /**
     * @var string
     */
    protected $_actionPath;
    /**
     * @var array
     */
    protected $_actionParams;

    /**
     * @param string $json
     */
    protected function _parseJson(
        $json)
    {
        $data = Json::decode($json);

        $this->_json = $json;

        if (isset($data->t)) {
            $this->_title = (string) $data->t;
        }
        if (isset($data->m)) {
            $this->_message = (string) $data->m;
        }
        if (isset($data->tt)) {
            $this->_tickerText = (string) $data->tt;
        }
        $this->_actionPath = '';
        $this->_actionParams = [];
        if (isset($data->ig)) {
            $this->_igAction = (string) $data->ig;
            $parts = parse_url($this->_igAction);
            if (isset($parts['path'])) {
                $this->_actionPath = $parts['path'];
            }
            if (isset($parts['query'])) {
                parse_str($parts['query'], $this->_actionParams);
            }
        }
        if (isset($data->collapse_key)) {
            $this->_collapseKey = (string) $data->collapse_key;
        }
        if (isset($data->i)) {
            $this->_optionalImage = (string) $data->i;
        }
        if (isset($data->a)) {
            $this->_optionalAvatarUrl = (string) $data->a;
        }
        if (isset($data->sound)) {
            $this->_sound = (string) $data->sound;
        }
        if (isset($data->pi)) {
            $this->_pushId = (string) $data->pi;
        }
        if (isset($data->c)) {
            $this->_pushCategory = (string) $data->c;
        }
        if (isset($data->u)) {
            $this->_intendedRecipientUserId = (string) $data->u;
        }
        if (isset($data->s)) {
            $this->_sourceUserId = (string) $data->s;
        }
        if (isset($data->igo)) {
            $this->_igActionOverride = (string) $data->igo;
        }
        if (isset($data->bc)) {
            $this->_badgeCount = new BadgeCount((string) $data->bc);
        }
        if (isset($data->ia)) {
            $this->_inAppActors = (string) $data->ia;
        }
    }

    /**
     * Notification constructor.
     *
     * @param string $json
     */
    public function __construct(
        $json)
    {
        $this->_parseJson($json);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->_json;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @return string
     */
    public function getTickerText()
    {
        return $this->_tickerText;
    }

    /**
     * @return string
     */
    public function getIgAction()
    {
        return $this->_igAction;
    }

    /**
     * @return string
     */
    public function getIgActionOverride()
    {
        return $this->_igActionOverride;
    }

    /**
     * @return string
     */
    public function getOptionalImage()
    {
        return $this->_optionalImage;
    }

    /**
     * @return string
     */
    public function getOptionalAvatarUrl()
    {
        return $this->_optionalAvatarUrl;
    }

    /**
     * @return string
     */
    public function getCollapseKey()
    {
        return $this->_collapseKey;
    }

    /**
     * @return string
     */
    public function getSound()
    {
        return $this->_sound;
    }

    /**
     * @return string
     */
    public function getPushId()
    {
        return $this->_pushId;
    }

    /**
     * @return string
     */
    public function getPushCategory()
    {
        return $this->_pushCategory;
    }

    /**
     * @return string
     */
    public function getIntendedRecipientUserId()
    {
        return $this->_intendedRecipientUserId;
    }

    /**
     * @return string
     */
    public function getSourceUserId()
    {
        return $this->_sourceUserId;
    }

    /**
     * @return BadgeCount
     */
    public function getBadgeCount()
    {
        return $this->_badgeCount;
    }

    /**
     * @return string
     */
    public function getInAppActors()
    {
        return $this->_inAppActors;
    }

    /**
     * @return string
     */
    public function getActionPath()
    {
        return $this->_actionPath;
    }

    /**
     * @return array
     */
    public function getActionParams()
    {
        return $this->_actionParams;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getActionParam(
        $key)
    {
        return isset($this->_actionParams[$key]) ? $this->_actionParams[$key] : null;
    }
}
