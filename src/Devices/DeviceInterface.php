<?php

namespace InstagramAPI\Devices;

interface DeviceInterface
{
    /**
     * Get the device identity string.
     *
     * @return string
     */
    public function getDeviceString();

    /**
     * Get the HTTP user-agent string.
     *
     * @return string
     */
    public function getUserAgent();

    /**
     * Get the Facebook user-agent string.
     *
     * @param string $appName Application name.
     *
     * @return string
     */
    public function getFbUserAgent(
        $appName);

    /**
     * Get the Android SDK/API version.
     *
     * @return string
     */
    public function getAndroidVersion();

    /**
     * Get the Android release version.
     *
     * @return string
     */
    public function getAndroidRelease();

    /**
     * Get the display DPI (with "dpi" suffix).
     *
     * @return string
     */
    public function getDPI();

    /**
     * Get the display resolution (width x height).
     *
     * @return string
     */
    public function getResolution();

    /**
     * Get the manufacturer.
     *
     * @return string
     */
    public function getManufacturer();

    /**
     * Get the brand (optional).
     *
     * @return string|null
     */
    public function getBrand();

    /**
     * Get the hardware model.
     *
     * @return string
     */
    public function getModel();

    /**
     * Get the hardware device code.
     *
     * @return string
     */
    public function getDevice();

    /**
     * Get the hardware CPU code.
     *
     * @return string
     */
    public function getCPU();
}
