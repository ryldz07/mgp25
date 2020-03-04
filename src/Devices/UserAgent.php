<?php

namespace InstagramAPI\Devices;

use InstagramAPI\Constants;

/**
 * Android device User-Agent builder.
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class UserAgent
{
    /**
     * How to format the user agent string.
     *
     * @var string
     */
    const USER_AGENT_FORMAT = 'Instagram %s Android (%s/%s; %s; %s; %s; %s; %s; %s; %s; %s)';

    /**
     * Generates a User Agent string from a DeviceInterface.
     *
     * @param string          $appVersion Instagram client app version.
     * @param string          $userLocale The user's locale, such as "en_US".
     * @param DeviceInterface $device
     *
     * @return string
     */
    public static function buildUserAgent(
        $appVersion,
        $userLocale,
        DeviceInterface $device)
    {
        // Build the appropriate "Manufacturer" or "Manufacturer/Brand" string.
        $manufacturerWithBrand = $device->getManufacturer();
        if ($device->getBrand() !== null) {
            $manufacturerWithBrand .= '/'.$device->getBrand();
        }

        // Generate the final User-Agent string.
        return sprintf(
            self::USER_AGENT_FORMAT,
            $appVersion, // App version ("27.0.0.7.97").
            $device->getAndroidVersion(),
            $device->getAndroidRelease(),
            $device->getDPI(),
            $device->getResolution(),
            $manufacturerWithBrand,
            $device->getModel(),
            $device->getDevice(),
            $device->getCPU(),
            $userLocale, // Locale ("en_US").
            Constants::VERSION_CODE
        );
    }

    /**
     * Escape string for Facebook User-Agent string.
     *
     * @param $string
     *
     * @return string
     */
    protected static function _escapeFbString(
        $string)
    {
        $result = '';
        for ($i = 0; $i < strlen($string); ++$i) {
            $char = $string[$i];
            if ($char === '&') {
                $result .= '&amp;';
            } elseif ($char < ' ' || $char > '~') {
                $result .= sprintf('&#%d;', ord($char));
            } else {
                $result .= $char;
            }
        }
        $result = strtr($result, ['/' => '-', ';' => '-']);

        return $result;
    }

    /**
     * Generates a FB User Agent string from a DeviceInterface.
     *
     * @param string          $appName     Application name.
     * @param string          $appVersion  Instagram client app version.
     * @param string          $versionCode Instagram client app version code.
     * @param string          $userLocale  The user's locale, such as "en_US".
     * @param DeviceInterface $device
     *
     * @throws \InvalidArgumentException If the device parameter is invalid.
     *
     * @return string
     */
    public static function buildFbUserAgent(
        $appName,
        $appVersion,
        $versionCode,
        $userLocale,
        DeviceInterface $device)
    {
        list($width, $height) = explode('x', $device->getResolution());
        $density = round(str_replace('dpi', '', $device->getDPI()) / 160, 1);
        $result = [
            'FBAN' => $appName,
            'FBAV' => $appVersion,
            'FBBV' => $versionCode,
            'FBDM' => sprintf('{density=%.1f,width=%d,height=%d}', $density, $width, $height),
            'FBLC' => $userLocale,
            'FBCR' => '', // We don't have cellular.
            'FBMF' => self::_escapeFbString($device->getManufacturer()),
            'FBBD' => self::_escapeFbString($device->getBrand() ? $device->getBrand() : $device->getManufacturer()),
            'FBPN' => Constants::PACKAGE_NAME,
            'FBDV' => self::_escapeFbString($device->getModel()),
            'FBSV' => self::_escapeFbString($device->getAndroidRelease()),
            'FBLR' => 0, // android.hardware.ram.low
            'FBBK' => 1, // Const (at least in 10.12.0).
            'FBCA' => self::_escapeFbString(GoodDevices::CPU_ABI),
        ];
        array_walk($result, function (&$value, $key) {
            $value = sprintf('%s/%s', $key, $value);
        });

        // Trailing semicolon is essential.
        return '['.implode(';', $result).';]';
    }
}
