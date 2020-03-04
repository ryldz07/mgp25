<?php

namespace InstagramAPI\Push\Fbns;

use Fbns\Client\Auth\DeviceAuth;
use Fbns\Client\AuthInterface;
use InstagramAPI\Instagram;

class Auth implements AuthInterface
{
    /**
     * @var Instagram
     */
    protected $_instagram;

    /**
     * @var DeviceAuth
     */
    protected $_deviceAuth;

    /**
     * Auth constructor.
     *
     * @param Instagram $instagram
     */
    public function __construct(
        Instagram $instagram)
    {
        $this->_instagram = $instagram;
        $this->_deviceAuth = $this->_instagram->settings->getFbnsAuth();
    }

    /** {@inheritdoc} */
    public function getClientId()
    {
        return $this->_deviceAuth->getClientId();
    }

    /** {@inheritdoc} */
    public function getClientType()
    {
        return $this->_deviceAuth->getClientType();
    }

    /** {@inheritdoc} */
    public function getUserId()
    {
        return $this->_deviceAuth->getUserId();
    }

    /** {@inheritdoc} */
    public function getPassword()
    {
        return $this->_deviceAuth->getPassword();
    }

    /** {@inheritdoc} */
    public function getDeviceId()
    {
        return $this->_deviceAuth->getDeviceId();
    }

    /** {@inheritdoc} */
    public function getDeviceSecret()
    {
        return $this->_deviceAuth->getDeviceSecret();
    }

    /** {@inheritdoc} */
    public function __toString()
    {
        return $this->_deviceAuth->__toString();
    }

    /**
     * Update auth data.
     *
     * @param string $auth
     *
     * @throws \InvalidArgumentException
     */
    public function update(
        $auth)
    {
        /* @var DeviceAuth $auth */
        $this->_deviceAuth->read($auth);
        $this->_instagram->settings->setFbnsAuth($this->_deviceAuth);
    }
}
