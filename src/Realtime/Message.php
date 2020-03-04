<?php

namespace InstagramAPI\Realtime;

class Message
{
    /**
     * @var string
     */
    private $_module;

    /**
     * @var mixed
     */
    private $_data;

    /**
     * Message constructor.
     *
     * @param string $module
     * @param mixed  $payload
     */
    public function __construct(
        $module,
        $payload)
    {
        $this->_module = $module;
        $this->_data = $payload;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }
}
