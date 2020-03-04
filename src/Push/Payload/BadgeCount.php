<?php

namespace InstagramAPI\Push\Payload;

use Fbns\Client\Json;

class BadgeCount
{
    /**
     * @var string
     */
    protected $_json;

    /**
     * @var int
     */
    protected $_direct;
    /**
     * @var int
     */
    protected $_ds;
    /**
     * @var int
     */
    protected $_activities;

    /**
     * @param string $json
     */
    protected function _parseJson(
        $json)
    {
        $data = Json::decode($json);
        $this->_json = $json;

        if (isset($data->di)) {
            $this->_direct = (int) $data->di;
        }
        if (isset($data->ds)) {
            $this->_ds = (int) $data->ds;
        }
        if (isset($data->ac)) {
            $this->_activities = (int) $data->ac;
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
     * @return int
     */
    public function getDirect()
    {
        return $this->_direct;
    }

    /**
     * @return int
     */
    public function getDs()
    {
        return $this->_ds;
    }

    /**
     * @return int
     */
    public function getActivities()
    {
        return $this->_activities;
    }
}
