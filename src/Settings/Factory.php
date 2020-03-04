<?php

namespace InstagramAPI\Settings;

use InstagramAPI\Exception\SettingsException;

/**
 * Effortlessly instantiates a StorageHandler with the desired Storage backend.
 *
 * Takes care of resolving user configuration values from multiple sources, then
 * creates the correct Storage backend and hooks it up to a StorageHandler.
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class Factory
{
    /**
     * Create a StorageHandler instance.
     *
     * @param array $storageConfig Configuration for the desired
     *                             user settings storage backend.
     * @param array $callbacks     Optional StorageHandler callback functions.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return \InstagramAPI\Settings\StorageHandler
     */
    public static function createHandler(
        array $storageConfig,
        array $callbacks = [])
    {
        // Resolve the storage backend choice if none provided in array.
        if (!isset($storageConfig['storage'])) {
            $cmdOptions = self::getCmdOptions([
                'settings_storage::',
            ]);
            $storage = self::getUserConfig('storage', $storageConfig, $cmdOptions);
            if ($storage === null) {
                $storage = 'file';
            }
            $storageConfig = ['storage' => $storage];
        }

        // Determine the user's final storage configuration.
        switch ($storageConfig['storage']) {
        case 'file':
            // Look for allowed command-line values related to this backend.
            $cmdOptions = self::getCmdOptions([
                'settings_basefolder::',
            ]);

            // These settings are optional:
            $baseFolder = self::getUserConfig('basefolder', $storageConfig, $cmdOptions);

            // Generate the final storage location configuration.
            $locationConfig = [
                'basefolder' => $baseFolder,
            ];

            $storageInstance = new Storage\File();
            break;
        case 'mysql':
            // Look for allowed command-line values related to this backend.
            $cmdOptions = self::getCmdOptions([
                'settings_dbusername::',
                'settings_dbpassword::',
                'settings_dbhost::',
                'settings_dbname::',
                'settings_dbtablename::',
            ]);

            // These settings are optional, and can be provided regardless of
            // connection method:
            $locationConfig = [
                'dbtablename' => self::getUserConfig('dbtablename', $storageConfig, $cmdOptions),
            ];

            // These settings are required, but you only have to use one method:
            if (isset($storageConfig['pdo'])) {
                // If "pdo" is set in the factory config, assume the user wants
                // to re-use an existing PDO connection. In that case we ignore
                // the username/password/host/name parameters and use their PDO.
                // NOTE: Beware that we WILL change attributes on the PDO
                // connection to suit our needs! Primarily turning all error
                // reporting into exceptions, and setting the charset to UTF-8.
                // If you want to re-use a PDO connection, you MUST accept the
                // fact that WE NEED exceptions and UTF-8 in our PDO! If that is
                // not acceptable to you then DO NOT re-use your own PDO object!
                $locationConfig['pdo'] = $storageConfig['pdo'];
            } else {
                // Make a new connection. Optional settings for it:
                $locationConfig['dbusername'] = self::getUserConfig('dbusername', $storageConfig, $cmdOptions);
                $locationConfig['dbpassword'] = self::getUserConfig('dbpassword', $storageConfig, $cmdOptions);
                $locationConfig['dbhost'] = self::getUserConfig('dbhost', $storageConfig, $cmdOptions);
                $locationConfig['dbname'] = self::getUserConfig('dbname', $storageConfig, $cmdOptions);
            }

            $storageInstance = new Storage\MySQL();
            break;
        case 'sqlite':
            // Look for allowed command-line values related to this backend.
            $cmdOptions = self::getCmdOptions([
                'settings_dbfilename::',
                'settings_dbtablename::',
            ]);

            // These settings are optional, and can be provided regardless of
            // connection method:
            $locationConfig = [
                'dbtablename' => self::getUserConfig('dbtablename', $storageConfig, $cmdOptions),
            ];

            // These settings are required, but you only have to use one method:
            if (isset($storageConfig['pdo'])) {
                // If "pdo" is set in the factory config, assume the user wants
                // to re-use an existing PDO connection. In that case we ignore
                // the SQLite filename/connection parameters and use their PDO.
                // NOTE: Beware that we WILL change attributes on the PDO
                // connection to suit our needs! Primarily turning all error
                // reporting into exceptions, and setting the charset to UTF-8.
                // If you want to re-use a PDO connection, you MUST accept the
                // fact that WE NEED exceptions and UTF-8 in our PDO! If that is
                // not acceptable to you then DO NOT re-use your own PDO object!
                $locationConfig['pdo'] = $storageConfig['pdo'];
            } else {
                // Make a new connection. Optional settings for it:
                $locationConfig['dbfilename'] = self::getUserConfig('dbfilename', $storageConfig, $cmdOptions);
            }

            $storageInstance = new Storage\SQLite();
            break;
        case 'memcached':
            // The memcached storage can only be configured via the factory
            // configuration array (not via command line or environment vars).

            // These settings are required, but you only have to use one method:
            if (isset($storageConfig['memcached'])) {
                // Re-use the user's own Memcached object.
                $locationConfig = [
                    'memcached' => $storageConfig['memcached'],
                ];
            } else {
                // Make a new connection. Optional settings for it:
                $locationConfig = [
                    // This ID will be passed to the \Memcached() constructor.
                    // NOTE: Memcached's "persistent ID" feature makes Memcached
                    // keep the settings even after you disconnect.
                    'persistent_id' => (isset($storageConfig['persistent_id'])
                                        ? $storageConfig['persistent_id']
                                        : null),
                    // Array which will be passed to \Memcached::setOptions().
                    'memcached_options' => (isset($storageConfig['memcached_options'])
                                            ? $storageConfig['memcached_options']
                                            : null),
                    // Array which will be passed to \Memcached::addServers().
                    // NOTE: Can contain one or multiple servers.
                    'servers' => (isset($storageConfig['servers'])
                                  ? $storageConfig['servers']
                                  : null),
                    // SASL username and password to be used for SASL
                    // authentication with all of the Memcached servers.
                    // NOTE: PHP's Memcached API doesn't support individual
                    // authentication credentials per-server, so these values
                    // apply to all of your servers if you use this feature!
                    'sasl_username' => (isset($storageConfig['sasl_username'])
                                        ? $storageConfig['sasl_username']
                                        : null),
                    'sasl_password' => (isset($storageConfig['sasl_password'])
                                        ? $storageConfig['sasl_password']
                                        : null),
                ];
            }

            $storageInstance = new Storage\Memcached();
            break;
        case 'custom':
            // Custom storage classes can only be configured via the main array.
            // When using a custom class, you must provide a StorageInterface:
            if (!isset($storageConfig['class'])
                || !$storageConfig['class'] instanceof StorageInterface) {
                throw new SettingsException('Invalid custom storage class.');
            }

            // Create a clean storage location configuration array.
            $locationConfig = $storageConfig;
            unset($locationConfig['storage']);
            unset($locationConfig['class']);

            $storageInstance = $storageConfig['class'];
            break;
        default:
            throw new SettingsException(sprintf(
                'Unknown settings storage type "%s".',
                $storageConfig['storage']
            ));
        }

        // Create the storage handler and connect to the storage location.
        return new StorageHandler(
            $storageInstance,
            $locationConfig,
            $callbacks
        );
    }

    /**
     * Get option values via command-line parameters.
     *
     * @param array $longOpts The longnames for the options to look for.
     *
     * @return array
     */
    public static function getCmdOptions(
        array $longOpts)
    {
        $cmdOptions = getopt('', $longOpts);
        if (!is_array($cmdOptions)) {
            $cmdOptions = [];
        }

        return $cmdOptions;
    }

    /**
     * Looks for the highest-priority result for a Storage config value.
     *
     * @param string $settingName   The name of the setting.
     * @param array  $storageConfig The Factory's configuration array.
     * @param array  $cmdOptions    All parsed command-line options.
     *
     * @return string|null The value if found, otherwise NULL.
     */
    public static function getUserConfig(
        $settingName,
        array $storageConfig,
        array $cmdOptions)
    {
        // Command line options have the highest precedence.
        // NOTE: Settings provided via cmd must have a "settings_" prefix.
        if (array_key_exists("settings_{$settingName}", $cmdOptions)) {
            return $cmdOptions["settings_{$settingName}"];
        }

        // Environment variables have the second highest precedence.
        // NOTE: Settings provided via env must be UPPERCASED and have
        // a "SETTINGS_" prefix, for example "SETTINGS_STORAGE".
        $envValue = getenv('SETTINGS_'.strtoupper($settingName));
        if ($envValue !== false) {
            return $envValue;
        }

        // Our factory config array has the lowest precedence, so that you can
        // easily override it via the other methods when testing other storage
        // backends or different connection parameters.
        if (array_key_exists($settingName, $storageConfig)) {
            return $storageConfig[$settingName];
        }

        // Couldn't find any user-provided value. Automatically returns null.
        // NOTE: Damn you StyleCI for not allowing "return null;" for clarity.
    }
}
