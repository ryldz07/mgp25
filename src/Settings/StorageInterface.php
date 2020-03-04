<?php

namespace InstagramAPI\Settings;

/**
 * Data Storage Interface.
 *
 * These methods must be implemented by all storage backends. All of their input
 * and output validation is already done by the owning Settings class, so that
 * backends can focus on dealing with basic, fully verified data (instead of
 * writing and maintaining a bunch of boilerplate code to verify parameters).
 *
 * All functions in this file are listed in roughly the sequence they're called
 * during normal operation. You can completely trust that they are called in a
 * sane order and that you do not need to validate the call order.
 *
 * -- HOW TO STORE DATA: --
 *
 * The ONLY datatype we use for settings is STRINGS. You absolutely MUST store
 * ALL data as strings. Do NOT attempt any "fancy" formats such as "database INT
 * columns", because we will OFTEN be storing empty strings in all fields. Your
 * storage backends MUST use STRING storage and MUST allow EMPTY strings.
 *
 * Data length varies, but it's a VERY bad idea for you to cap the length. We
 * may need to store very long data someday. For example, the cookie data that
 * we already store is usually around 2000 characters long.
 *
 * Furthermore, do NOT make ANY assumptions about what settings-"keys/columns"
 * we will be storing. There is a list in StorageHandler::PERSISTENT_KEYS, but
 * it will DEFINITELY change several times in the future. If you hardcode those
 * columns, you will be in trouble in the future. Therefore, the absolute BEST
 * encoding method is to use a SINGLE file/database column for the settings and
 * to JSON-encode everything. And to use a SINGLE file/database column for the
 * cookies and simply store our raw cookie string as-provided.
 *
 * -- MAJOR WARNING: --
 *
 * Backend developers are NOT allowed to violate ANY aspect of this interface
 * specification. ANY exceptions thrown MUST be of the EXACT types listed, and
 * ALL return values MUST be of the EXACT types listed. This means that YOU will
 * have to handle ALL exceptions thrown by your particular backend solution, and
 * re-wrapping them as the required exceptions if necessary. Otherwise you WILL
 * BREAK all user applications that use your terrible Storage implementation.
 *
 * Just look at our existing Storage implementations to see correct code.
 */
interface StorageInterface
{
    /**
     * Connect to a storage location and perform necessary startup preparations.
     *
     * This function is guaranteed to be called before anything else here.
     *
     * Must do whatever needs to be done BEFORE any per-user data can be loaded
     * from/saved to the storage backend. For example, setting variables,
     * connecting to a database, creating tables, or loading from disk.
     *
     * You can treat this as your "constructor", but you may ALSO want a
     * separate constructor for any work that you DON'T want to repeat every
     * time that you're told to open a different storage location.
     *
     * However, you MUST put ALL per-storage startup code in THIS function,
     * since a normal constructor is only called a single time, as you're aware.
     *
     * @param array $locationConfig Configuration parameters for the location.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function openLocation(
        array $locationConfig);

    /**
     * Whether the storage backend contains a specific user.
     *
     * Having a user is defined as HAVING the USERSETTINGS-portion of the data.
     * The usercookies may be stored separately (such as in a cookiefile) and
     * are NOT essential since they can be re-generated. So the data storage
     * implementation MUST ONLY check for the existence of the USERSETTINGS.
     *
     * @param string $username The Instagram username.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return bool TRUE if user exists, otherwise FALSE.
     */
    public function hasUser(
        $username);

    /**
     * Move the internal data for a username to a new username.
     *
     * Is NEVER called for the currently loaded user, so Storage backend writers
     * can safely assume that you'll never be asked to rename the loaded user.
     *
     * Before performing the move, this function MUST validate that the OLD user
     * EXISTS and that the NEW user DOESN'T EXIST, and MUST throw an exception
     * if either of those checks fail. This is to ensure that users don't lose
     * data by accidentally overwriting something.
     *
     * @param string $oldUsername The old name that settings are stored as.
     * @param string $newUsername The new name to move the settings to.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function moveUser(
        $oldUsername,
        $newUsername);

    /**
     * Delete all internal data for a given username.
     *
     * Is NEVER called for the currently loaded user, so Storage backend writers
     * can safely assume that you'll never be asked to delete the loaded user.
     *
     * This function MUST treat a non-existent or already deleted user as
     * "success". ONLY throw an exception if ACTUAL deletion fails.
     *
     * @param string $username The Instagram username.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function deleteUser(
        $username);

    /**
     * Open the data storage for a specific user.
     *
     * If the user does not exist, THIS call MUST create their user storage, or
     * at least do any necessary preparations so that the other functions can
     * read/write to the user's storage (and behave as specified).
     *
     * Is called every time we're switching to a user, and happens before we
     * call any user-specific data retrieval functions.
     *
     * This function must cache the user reference and perform necessary backend
     * operations, such as opening file/database handles and finding the row ID
     * for the given user, so that all further queries know what user to use.
     *
     * All further calls relating to that user will assume that your storage
     * class has cached the user reference we gave you in this call.
     *
     * @param string $username The Instagram username.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function openUser(
        $username);

    /**
     * Load all settings for the currently active user.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return array An array with all current key-value pairs for the user, or
     *               an empty array if no settings exist.
     */
    public function loadUserSettings();

    /**
     * Save the settings for the currently active user.
     *
     * Is called every time any setting changes. The trigger-key can be used for
     * selectively saving only the modified setting. But most backends should
     * simply JSON-encode the whole $userSettings array and store that string.
     *
     * @param array  $userSettings An array with all of the user's key-value pairs.
     * @param string $triggerKey   The differing key which triggered the write.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function saveUserSettings(
        array $userSettings,
        $triggerKey);

    /**
     * Whether the storage backend has cookies for the currently active user.
     *
     * Even cookiefile (file-based jars) MUST answer this question, for example
     * by checking if their desired cookiefile exists and is non-empty. And all
     * other storage backends (such as databases) MUST also verify that their
     * existing cookie data is non-empty.
     *
     * Don't validate the actual cookie contents, just look for non-zero size!
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return bool TRUE if cookies exist, otherwise FALSE.
     */
    public function hasUserCookies();

    /**
     * Get the cookiefile disk path (only if a file-based cookie jar is wanted).
     *
     * The file does not have to exist yet. It will be created by the caller
     * on-demand when necessary.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return string|null Either a non-empty string file-path to use a
     *                     file-based cookie jar which the CALLER will
     *                     read/write, otherwise NULL (or any other
     *                     non-string value) if the BACKEND wants to
     *                     handle the cookies itself.
     */
    public function getUserCookiesFilePath();

    /**
     * (Non-cookiefile) Load all cookies for the currently active user.
     *
     * Note that this function is ONLY called if a non-string answer was
     * returned by the getUserCookiesFilePath() call.
     *
     * If your Storage backend class uses a cookiefile, make this a no-op.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     *
     * @return string|null A previously-stored raw cookie data string, or an
     *                     empty string/NULL if no data exists in the storage.
     */
    public function loadUserCookies();

    /**
     * (Non-cookiefile) Save all cookies for the currently active user.
     *
     * Note that this function is called frequently! But it is ONLY called if a
     * non-string answer was returned by the getUserCookiesFilePath() call.
     *
     * If your Storage backend class uses a cookiefile, make this a no-op.
     *
     * @param string $rawData An encoded string with all cookie data.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function saveUserCookies(
        $rawData);

    /**
     * Close the settings storage for the currently active user.
     *
     * Is called every time we're switching away from a user, BEFORE the new
     * user's loadUserSettings() call. Should be used for doing things like
     * closing previous per-user file handles in the backend, and unsetting the
     * cached user information that was set in the openUser() call.
     *
     * After this call, there will not be any other user-related calls until the
     * next openUser() call.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function closeUser();

    /**
     * Disconnect from a storage location and perform necessary shutdown steps.
     *
     * This function is called ONCE, when we no longer need to access the
     * currently open storage. But we may still open another storage afterwards,
     * so do NOT treat this as a "class destructor"!
     *
     * Implementing this is optional, but the function must exist.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function closeLocation();
}
