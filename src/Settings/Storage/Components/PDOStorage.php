<?php

namespace InstagramAPI\Settings\Storage\Components;

use InstagramAPI\Exception\SettingsException;
use InstagramAPI\Settings\StorageInterface;
use PDO;

/**
 * Re-usable PDO storage component, for easily building PDO-based backends.
 *
 * Any class derived from this base must implement the abstract methods.
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
abstract class PDOStorage implements StorageInterface
{
    /** @var string Human name of the backend, such as "MySQL" or "SQLite". */
    protected $_backendName;

    /** @var \PDO Our connection to the database. */
    protected $_pdo;

    /** @var bool Whether we own the PDO connection or are borrowing it. */
    protected $_isSharedPDO;

    /** @var string Which table to store the settings in. */
    protected $_dbTableName;

    /** @var string Current Instagram username that all settings belong to. */
    protected $_username;

    /** @var array A cache of important columns from the user's database row. */
    protected $_cache;

    /**
     * Constructor.
     *
     * @param string $backendName Human name of the backend, such as "MySQL" or "SQLite".
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function __construct(
        $backendName = 'PDO')
    {
        $this->_backendName = $backendName;
    }

    /**
     * Connect to a storage location and perform necessary startup preparations.
     *
     * {@inheritdoc}
     */
    public function openLocation(
        array $locationConfig)
    {
        $this->_dbTableName = (isset($locationConfig['dbtablename'])
                               ? $locationConfig['dbtablename']
                               : 'user_sessions');

        if (isset($locationConfig['pdo'])) {
            // Pre-provided connection to re-use instead of creating a new one.
            if (!$locationConfig['pdo'] instanceof PDO) {
                throw new SettingsException('The custom PDO object is invalid.');
            }
            $this->_isSharedPDO = true;
            $this->_pdo = $locationConfig['pdo'];
        } else {
            // We should connect for the user, by creating our own PDO object.
            $this->_isSharedPDO = false;

            try {
                $this->_pdo = $this->_createPDO($locationConfig);
            } catch (\Exception $e) {
                throw new SettingsException($this->_backendName.' Connection Failed: '.$e->getMessage());
            }
        }

        try {
            $this->_configurePDO();
        } catch (\Exception $e) {
            throw new SettingsException($this->_backendName.' Configuration Failed: '.$e->getMessage());
        }

        try {
            $this->_autoCreateTable();
        } catch (\Exception $e) {
            throw new SettingsException($this->_backendName.' Error: '.$e->getMessage());
        }
    }

    /**
     * Create a new PDO connection to the database.
     *
     * @param array $locationConfig Configuration parameters for the location.
     *
     * @throws \Exception
     *
     * @return \PDO The database connection.
     */
    abstract protected function _createPDO(
        array $locationConfig);

    /**
     * Configures the connection for our needs.
     *
     * Warning for those who re-used a PDO object: Beware that we WILL change
     * attributes on the PDO connection to suit our needs! Primarily turning all
     * error reporting into exceptions, and setting the charset to UTF-8. If you
     * want to re-use a PDO connection, you MUST accept the fact that WE NEED
     * exceptions and UTF-8 in our PDO! If that is not acceptable to you then DO
     * NOT re-use your own PDO object!
     *
     * @throws \Exception
     */
    protected function _configurePDO()
    {
        $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->_enableUTF8();
    }

    /**
     * Enable UTF-8 encoding on the connection.
     *
     * This is database-specific and usually requires some kind of query.
     *
     * @throws \Exception
     */
    abstract protected function _enableUTF8();

    /**
     * Automatically create the database table if necessary.
     *
     * @throws \Exception
     */
    abstract protected function _autoCreateTable();

    /**
     * Automatically writes to the correct user's row and caches the new value.
     *
     * @param string $column The database column.
     * @param string $data   Data to be written.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    protected function _setUserColumn(
        $column,
        $data)
    {
        if ($column != 'settings' && $column != 'cookies') {
            throw new SettingsException(sprintf(
                'Attempt to write to illegal database column "%s".',
                $column
            ));
        }

        try {
            // Update if the user row already exists, otherwise insert.
            $binds = [':data' => $data];
            if ($this->_cache['id'] !== null) {
                $sql = "UPDATE `{$this->_dbTableName}` SET {$column}=:data WHERE (id=:id)";
                $binds[':id'] = $this->_cache['id'];
            } else {
                $sql = "INSERT INTO `{$this->_dbTableName}` (username, {$column}) VALUES (:username, :data)";
                $binds[':username'] = $this->_username;
            }

            $sth = $this->_pdo->prepare($sql);
            $sth->execute($binds);

            // Keep track of the database row ID for the user.
            if ($this->_cache['id'] === null) {
                $this->_cache['id'] = $this->_pdo->lastinsertid();
            }

            $sth->closeCursor();

            // Cache the new value.
            $this->_cache[$column] = $data;
        } catch (\Exception $e) {
            throw new SettingsException($this->_backendName.' Error: '.$e->getMessage());
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
        // Check whether a row exists for that username.
        $sth = $this->_pdo->prepare("SELECT EXISTS(SELECT 1 FROM `{$this->_dbTableName}` WHERE (username=:username))");
        $sth->execute([':username' => $username]);
        $result = $sth->fetchColumn();
        $sth->closeCursor();

        return $result > 0 ? true : false;
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
        try {
            // Verify that the old username exists.
            if (!$this->hasUser($oldUsername)) {
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

            // Now attempt to rename the old username column to the new name.
            $sth = $this->_pdo->prepare("UPDATE `{$this->_dbTableName}` SET username=:newusername WHERE (username=:oldusername)");
            $sth->execute([':oldusername' => $oldUsername, ':newusername' => $newUsername]);
            $sth->closeCursor();
        } catch (SettingsException $e) {
            throw $e; // Ugly but necessary to re-throw only our own messages.
        } catch (\Exception $e) {
            throw new SettingsException($this->_backendName.' Error: '.$e->getMessage());
        }
    }

    /**
     * Delete all internal data for a given username.
     *
     * {@inheritdoc}
     */
    public function deleteUser(
        $username)
    {
        try {
            // Just attempt to delete the row. Doesn't error if already missing.
            $sth = $this->_pdo->prepare("DELETE FROM `{$this->_dbTableName}` WHERE (username=:username)");
            $sth->execute([':username' => $username]);
            $sth->closeCursor();
        } catch (\Exception $e) {
            throw new SettingsException($this->_backendName.' Error: '.$e->getMessage());
        }
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

        // Retrieve and cache the existing user data row if available.
        try {
            $sth = $this->_pdo->prepare("SELECT id, settings, cookies FROM `{$this->_dbTableName}` WHERE (username=:username)");
            $sth->execute([':username' => $this->_username]);
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            $sth->closeCursor();

            if (is_array($result)) {
                $this->_cache = $result;
            } else {
                $this->_cache = [
                    'id'       => null,
                    'settings' => null,
                    'cookies'  => null,
                ];
            }
        } catch (\Exception $e) {
            throw new SettingsException($this->_backendName.' Error: '.$e->getMessage());
        }
    }

    /**
     * Load all settings for the currently active user.
     *
     * {@inheritdoc}
     */
    public function loadUserSettings()
    {
        $userSettings = [];

        if (!empty($this->_cache['settings'])) {
            $userSettings = @json_decode($this->_cache['settings'], true, 512, JSON_BIGINT_AS_STRING);
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
        $this->_setUserColumn('settings', $encodedData);
    }

    /**
     * Whether the storage backend has cookies for the currently active user.
     *
     * {@inheritdoc}
     */
    public function hasUserCookies()
    {
        return isset($this->_cache['cookies'])
                && !empty($this->_cache['cookies']);
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
        return isset($this->_cache['cookies'])
                ? $this->_cache['cookies']
                : null;
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
        $this->_setUserColumn('cookies', $rawData);
    }

    /**
     * Close the settings storage for the currently active user.
     *
     * {@inheritdoc}
     */
    public function closeUser()
    {
        $this->_username = null;
        $this->_cache = null;
    }

    /**
     * Disconnect from a storage location and perform necessary shutdown steps.
     *
     * {@inheritdoc}
     */
    public function closeLocation()
    {
        // Delete our reference to the PDO object. If nobody else references
        // it, the PDO connection will now be terminated. In case of shared
        // objects, the original owner still has their reference (as intended).
        $this->_pdo = null;
    }
}
