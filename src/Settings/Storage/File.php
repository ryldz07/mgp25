<?php

namespace InstagramAPI\Settings\Storage;

use InstagramAPI\Constants;
use InstagramAPI\Exception\SettingsException;
use InstagramAPI\Settings\StorageInterface;
use InstagramAPI\Utils;

/**
 * Persistent storage backend which keeps settings in a reliable binary file.
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class File implements StorageInterface
{
    /** @var int Current storage format version. */
    const STORAGE_VERSION = 2;

    /** @var string Format for settings filename. */
    const SETTINGSFILE_NAME = '%s-settings.dat';

    /** @var string Format for cookie jar filename. */
    const COOKIESFILE_NAME = '%s-cookies.dat';

    /** @var string The base folder for all storage files. */
    private $_baseFolder;

    /** @var string The folder for the current user's storage. */
    private $_userFolder;

    /** @var string Path to the current user's settings file. */
    private $_settingsFile;

    /** @var string Path to the current user's cookie jar file. */
    private $_cookiesFile;

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
        // Determine which base folder to store all per-user data in.
        $baseFolder = ((isset($locationConfig['basefolder'])
                        && !empty($locationConfig['basefolder']))
                       ? $locationConfig['basefolder']
                       : Constants::SRC_DIR.'/../sessions');
        // Create the base folder and normalize its path to a clean value.
        $this->_baseFolder = $this->_createFolder($baseFolder);
    }

    /**
     * Whether the storage backend contains a specific user.
     *
     * {@inheritdoc}
     */
    public function hasUser(
        $username)
    {
        // Check whether the user's settings-file exists.
        $hasUser = $this->_generateUserPaths($username);

        return is_file($hasUser['settingsFile']) ? true : false;
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
        // Verify the old and new username parameters.
        $oldUser = $this->_generateUserPaths($oldUsername);
        $newUser = $this->_generateUserPaths($newUsername);
        if (!is_dir($oldUser['userFolder'])) {
            throw new SettingsException(sprintf(
                'Cannot move non-existent user folder "%s".',
                $oldUser['userFolder']
            ));
        }
        if (is_dir($newUser['userFolder'])) {
            throw new SettingsException(sprintf(
                'Refusing to overwrite existing user folder "%s".',
                $newUser['userFolder']
            ));
        }

        // Create the new destination folder and migrate all data.
        $this->_createFolder($newUser['userFolder']);
        if (is_file($oldUser['settingsFile'])
            && !@rename($oldUser['settingsFile'], $newUser['settingsFile'])) {
            throw new SettingsException(sprintf(
                'Failed to move "%s" to "%s".',
                $oldUser['settingsFile'], $newUser['settingsFile']
            ));
        }
        if (is_file($oldUser['cookiesFile'])
            && !@rename($oldUser['cookiesFile'], $newUser['cookiesFile'])) {
            throw new SettingsException(sprintf(
                'Failed to move "%s" to "%s".',
                $oldUser['cookiesFile'], $newUser['cookiesFile']
            ));
        }

        // Delete all files in the old folder, and the folder itself.
        Utils::deleteTree($oldUser['userFolder']);
    }

    /**
     * Delete all internal data for a given username.
     *
     * {@inheritdoc}
     */
    public function deleteUser(
        $username)
    {
        // Delete all files in the user folder, and the folder itself.
        $delUser = $this->_generateUserPaths($username);
        Utils::deleteTree($delUser['userFolder']);
    }

    /**
     * Open the data storage for a specific user.
     *
     * {@inheritdoc}
     */
    public function openUser(
        $username)
    {
        $this->_username = $username;
        $userPaths = $this->_generateUserPaths($username);
        $this->_userFolder = $userPaths['userFolder'];
        $this->_settingsFile = $userPaths['settingsFile'];
        $this->_cookiesFile = $userPaths['cookiesFile'];
        $this->_createFolder($this->_userFolder);
    }

    /**
     * Load all settings for the currently active user.
     *
     * {@inheritdoc}
     */
    public function loadUserSettings()
    {
        $userSettings = [];

        if (!is_file($this->_settingsFile)) {
            return $userSettings; // Nothing to load.
        }

        // Read from disk.
        $rawData = @file_get_contents($this->_settingsFile);
        if ($rawData === false) {
            throw new SettingsException(sprintf(
                'Unable to read from settings file "%s".',
                $this->_settingsFile
            ));
        }

        // Fetch the data version ("FILESTORAGEv#;") header.
        $dataVersion = 1; // Assume migration from v1 if no version.
        if (preg_match('/^FILESTORAGEv(\d+);/', $rawData, $matches)) {
            $dataVersion = intval($matches[1]);
            $rawData = substr($rawData, strpos($rawData, ';') + 1);
        }

        // Decode the key-value pairs regardless of data-storage version.
        $userSettings = $this->_decodeStorage($dataVersion, $rawData);

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
        // Generate the storage version header.
        $versionHeader = 'FILESTORAGEv'.self::STORAGE_VERSION.';';

        // Encode a binary representation of all settings.
        // VERSION 2 STORAGE FORMAT: JSON-encoded blob.
        $encodedData = $versionHeader.json_encode($userSettings);

        // Perform an atomic diskwrite, which prevents accidental truncation.
        // NOTE: If we had just written directly to settingsPath, the file would
        // have become corrupted if the script was killed mid-write. The atomic
        // write process guarantees that the data is fully written to disk.
        Utils::atomicWrite($this->_settingsFile, $encodedData);
    }

    /**
     * Whether the storage backend has cookies for the currently active user.
     *
     * {@inheritdoc}
     */
    public function hasUserCookies()
    {
        return is_file($this->_cookiesFile)
            && filesize($this->_cookiesFile) > 0;
    }

    /**
     * Get the cookiefile disk path (only if a file-based cookie jar is wanted).
     *
     * {@inheritdoc}
     */
    public function getUserCookiesFilePath()
    {
        // Tell the caller to use a file-based cookie jar.
        return $this->_cookiesFile;
    }

    /**
     * (Non-cookiefile) Load all cookies for the currently active user.
     *
     * {@inheritdoc}
     */
    public function loadUserCookies()
    {
        // Never called for "cookiefile" format.
    }

    /**
     * (Non-cookiefile) Save all cookies for the currently active user.
     *
     * {@inheritdoc}
     */
    public function saveUserCookies(
        $rawData)
    {
        // Never called for "cookiefile" format.
    }

    /**
     * Close the settings storage for the currently active user.
     *
     * {@inheritdoc}
     */
    public function closeUser()
    {
        $this->_userFolder = null;
        $this->_settingsFile = null;
        $this->_cookiesFile = null;
        $this->_username = null;
    }

    /**
     * Disconnect from a storage location and perform necessary shutdown steps.
     *
     * {@inheritdoc}
     */
    public function closeLocation()
    {
        // We don't need to disconnect from anything since we are file-based.
    }

    /**
     * Decodes the data from any File storage format version.
     *
     * @param int    $dataVersion Which data format to decode.
     * @param string $rawData     The raw data, encoded in version's format.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return array An array with all current key-value pairs for the user.
     */
    private function _decodeStorage(
        $dataVersion,
        $rawData)
    {
        $loadedSettings = [];

        switch ($dataVersion) {
        case 1:
            /**
             * This is the old format from v1.x of Instagram-API.
             * Terrible format. Basic "key=value\r\n" and very fragile.
             */

            // Split by system-independent newlines. Tries \r\n (Win), then \r
            // (pre-2000s Mac), then \n\r, then \n (Mac OS X, UNIX, Linux).
            $lines = preg_split('/(\r\n?|\n\r?)/', $rawData, -1, PREG_SPLIT_NO_EMPTY);
            if ($lines !== false) {
                foreach ($lines as $line) {
                    // Key must be at least one character. Allows empty values.
                    if (preg_match('/^([^=]+)=(.*)$/', $line, $matches)) {
                        $key = $matches[1];
                        $value = rtrim($matches[2], "\r\n ");
                        $loadedSettings[$key] = $value;
                    }
                }
            }
            break;
        case 2:
            /**
             * Version 2 uses JSON encoding and perfectly stores any value.
             * And file corruption can't happen, thanks to the atomic writer.
             */
            $loadedSettings = @json_decode($rawData, true, 512, JSON_BIGINT_AS_STRING);
            if (!is_array($loadedSettings)) {
                throw new SettingsException(sprintf(
                    'Failed to decode corrupt settings file for account "%s".',
                    $this->_username
                ));
            }
            break;
        default:
            throw new SettingsException(sprintf(
                'Invalid file settings storage format version "%d".',
                $dataVersion
            ));
        }

        return $loadedSettings;
    }

    /**
     * Generates all path strings for a given username.
     *
     * @param string $username The Instagram username.
     *
     * @return array An array with information about the user's paths.
     */
    private function _generateUserPaths(
        $username)
    {
        $userFolder = $this->_baseFolder.'/'.$username;
        $settingsFile = $userFolder.'/'.sprintf(self::SETTINGSFILE_NAME, $username);
        $cookiesFile = $userFolder.'/'.sprintf(self::COOKIESFILE_NAME, $username);

        return [
            'userFolder'   => $userFolder,
            'settingsFile' => $settingsFile,
            'cookiesFile'  => $cookiesFile,
        ];
    }

    /**
     * Creates a folder if missing, or ensures that it is writable.
     *
     * @param string $folder The directory path.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return string The canonicalized absolute pathname of the folder, without
     *                any trailing slash.
     */
    private function _createFolder(
        $folder)
    {
        if (!Utils::createFolder($folder)) {
            throw new SettingsException(sprintf(
                'The "%s" folder is not writable.',
                $folder
            ));
        }

        // Determine the real path of the folder we created/checked.
        // NOTE: This ensures that the path will work even on stingy systems
        // such as Windows Server which chokes on multiple slashes in a row.
        $realPath = @realpath($folder);
        if (!is_string($realPath)) {
            throw new SettingsException(sprintf(
                'Unable to resolve real path to folder "%s".',
                $folder
            ));
        }

        return $realPath;
    }
}
