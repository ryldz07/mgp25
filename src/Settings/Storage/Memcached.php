<?php

namespace InstagramAPI\Settings\Storage;

use InstagramAPI\Exception\SettingsException;
use InstagramAPI\Settings\StorageInterface;
use Memcached as PHPMemcached;

/**
 * Semi-persistent storage backend which uses a Memcached server daemon.
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class Memcached implements StorageInterface
{
    /** @var \Memcached Our connection to the database. */
    private $_memcached;

    /** @var bool Whether we own Memcached's connection or are borrowing it. */
    private $_isSharedMemcached;

    /** @var string Current Instagram username that all settings belong to. */
    private $_username;

    /**
     * Connect to a storage location and perform necessary startup preparations.
     *
     * {@inheritdoc}
     */
    public function openLocation(
        array $locationConfig)
    {
        if (isset($locationConfig['memcached'])) {
            // Pre-provided connection to re-use instead of creating a new one.
            if (!$locationConfig['memcached'] instanceof PHPMemcached) {
                throw new SettingsException('The custom Memcached object is invalid.');
            }
            $this->_isSharedMemcached = true;
            $this->_memcached = $locationConfig['memcached'];
        } else {
            try {
                // Configure memcached with a persistent data retention ID.
                $this->_isSharedMemcached = false;
                $this->_memcached = new PHPMemcached(
                    is_string($locationConfig['persistent_id'])
                    ? $locationConfig['persistent_id']
                    : 'instagram'
                );

                // Enable SASL authentication if credentials were provided.
                // NOTE: PHP's Memcached API doesn't support individual
                // authentication credentials per-server!
                if (isset($locationConfig['sasl_username'])
                    && isset($locationConfig['sasl_password'])) {
                    // When SASL is used, the servers almost always NEED binary
                    // protocol, but if that doesn't work with the user's server
                    // then then the user can manually override this default by
                    // providing the "false" option via "memcached_options".
                    $this->_memcached->setOption(PHPMemcached::OPT_BINARY_PROTOCOL, true);
                    $this->_memcached->setSaslAuthData(
                        $locationConfig['sasl_username'],
                        $locationConfig['sasl_password']
                    );
                }

                // Apply any custom options the user has provided.
                // NOTE: This is where "OPT_BINARY_PROTOCOL" can be overridden.
                if (is_array($locationConfig['memcached_options'])) {
                    $this->_memcached->setOptions($locationConfig['memcached_options']);
                }

                // Add the provided servers to the pool.
                if (is_array($locationConfig['servers'])) {
                    $this->_memcached->addServers($locationConfig['servers']);
                } else {
                    // Use default port on localhost if nothing was provided.
                    $this->_memcached->addServer('localhost', 11211);
                }
            } catch (\Exception $e) {
                throw new SettingsException('Memcached Connection Failed: '.$e->getMessage());
            }
        }
    }

    /**
     * Retrieve a memcached key for a particular user.
     *
     * @param string $username The Instagram username.
     * @param string $key      Name of the subkey.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return string|null The value as a string IF the user's key exists,
     *                     otherwise NULL.
     */
    private function _getUserKey(
        $username,
        $key)
    {
        try {
            $realKey = $username.'_'.$key;
            $result = $this->_memcached->get($realKey);

            return $this->_memcached->getResultCode() !== PHPMemcached::RES_NOTFOUND
                    ? (string) $result
                    : null;
        } catch (\Exception $e) {
            throw new SettingsException('Memcached Error: '.$e->getMessage());
        }
    }

    /**
     * Set a memcached key for a particular user.
     *
     * @param string       $username The Instagram username.
     * @param string       $key      Name of the subkey.
     * @param string|mixed $value    The data to store. MUST be castable to string.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    private function _setUserKey(
        $username,
        $key,
        $value)
    {
        try {
            $realKey = $username.'_'.$key;
            $success = $this->_memcached->set($realKey, (string) $value);
            if (!$success) {
                throw new SettingsException(sprintf(
                    'Memcached failed to write to key "%s".',
                    $realKey
                ));
            }
        } catch (SettingsException $e) {
            throw $e; // Ugly but necessary to re-throw only our own messages.
        } catch (\Exception $e) {
            throw new SettingsException('Memcached Error: '.$e->getMessage());
        }
    }

    /**
     * Delete a memcached key for a particular user.
     *
     * @param string $username The Instagram username.
     * @param string $key      Name of the subkey.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    private function _delUserKey(
        $username,
        $key)
    {
        try {
            $realKey = $username.'_'.$key;
            $this->_memcached->delete($realKey);
        } catch (\Exception $e) {
            throw new SettingsException('Memcached Error: '.$e->getMessage());
        }
    }

    /**
     * Whether the storage backend contains a specific user.
     *
     * {@inheritdoc}
     */
    public function hasUser(
        $username)
    {
        // Check whether the user's settings exist (empty string allowed).
        $hasUser = $this->_getUserKey($username, 'settings');

        return $hasUser !== null ? true : false;
    }

    /**
     * Move the internal data for a username to a new username.
     *
     * {@inheritdoc}
     */
    public function moveUser(
        $oldUsername,
        $newUsername)
    {
        // Verify that the old username exists and fetch the old data.
        $oldSettings = $this->_getUserKey($oldUsername, 'settings');
        $oldCookies = $this->_getUserKey($oldUsername, 'cookies');
        if ($oldSettings === null) { // Only settings are vital.
            throw new SettingsException(sprintf(
                'Cannot move non-existent user "%s".',
                $oldUsername
            ));
        }

        // Verify that the new username does not exist.
        if ($this->hasUser($newUsername)) {
            throw new SettingsException(sprintf(
                'Refusing to overwrite existing user "%s".',
                $newUsername
            ));
        }

        // Now attempt to write all data to the new name.
        $this->_setUserKey($newUsername, 'settings', $oldSettings);
        if ($oldCookies !== null) { // Only if cookies existed.
            $this->_setUserKey($newUsername, 'cookies', $oldCookies);
        }

        // Delete the previous user keys.
        $this->deleteUser($oldUsername);
    }

    /**
     * Delete all internal data for a given username.
     *
     * {@inheritdoc}
     */
    public function deleteUser(
        $username)
    {
        $this->_delUserKey($username, 'settings');
        $this->_delUserKey($username, 'cookies');
    }

    /**
     * Open the data storage for a specific user.
     *
     * {@inheritdoc}
     */
    public function openUser(
        $username)
    {
        // Just cache the username. We'll create storage later if necessary.
        $this->_username = $username;
    }

    /**
     * Load all settings for the currently active user.
     *
     * {@inheritdoc}
     */
    public function loadUserSettings()
    {
        $userSettings = [];

        $encodedData = $this->_getUserKey($this->_username, 'settings');
        if (!empty($encodedData)) {
            $userSettings = @json_decode($encodedData, true, 512, JSON_BIGINT_AS_STRING);
            if (!is_array($userSettings)) {
                throw new SettingsException(sprintf(
                    'Failed to decode corrupt settings for account "%s".',
                    $this->_username
                ));
            }
        }

        return $userSettings;
    }

    /**
     * Save the settings for the currently active user.
     *
     * {@inheritdoc}
     */
    public function saveUserSettings(
        array $userSettings,
        $triggerKey)
    {
        // Store the settings as a JSON blob.
        $encodedData = json_encode($userSettings);
        $this->_setUserKey($this->_username, 'settings', $encodedData);
    }

    /**
     * Whether the storage backend has cookies for the currently active user.
     *
     * {@inheritdoc}
     */
    public function hasUserCookies()
    {
        // Simply check if the storage key for cookies exists and is non-empty.
        return !empty($this->loadUserCookies()) ? true : false;
    }

    /**
     * Get the cookiefile disk path (only if a file-based cookie jar is wanted).
     *
     * {@inheritdoc}
     */
    public function getUserCookiesFilePath()
    {
        // NULL = We (the backend) will handle the cookie loading/saving.
        return null;
    }

    /**
     * (Non-cookiefile) Load all cookies for the currently active user.
     *
     * {@inheritdoc}
     */
    public function loadUserCookies()
    {
        return $this->_getUserKey($this->_username, 'cookies');
    }

    /**
     * (Non-cookiefile) Save all cookies for the currently active user.
     *
     * {@inheritdoc}
     */
    public function saveUserCookies(
        $rawData)
    {
        // Store the raw cookie data as-provided.
        $this->_setUserKey($this->_username, 'cookies', $rawData);
    }

    /**
     * Close the settings storage for the currently active user.
     *
     * {@inheritdoc}
     */
    public function closeUser()
    {
        $this->_username = null;
    }

    /**
     * Disconnect from a storage location and perform necessary shutdown steps.
     *
     * {@inheritdoc}
     */
    public function closeLocation()
    {
        // Close all server connections if this was our own Memcached object.
        if (!$this->_isSharedMemcached) {
            try {
                $this->_memcached->quit();
            } catch (\Exception $e) {
                throw new SettingsException('Memcached Disconnection Failed: '.$e->getMessage());
            }
        }
        $this->_memcached = null;
    }
}
