<?php

namespace InstagramAPI;

/**
 * Instagram's Private API v6.1.0.
 *
 * TERMS OF USE:
 * - This code is in no way affiliated with, authorized, maintained, sponsored
 *   or endorsed by Instagram or any of its affiliates or subsidiaries. This is
 *   an independent and unofficial API. Use at your own risk.
 * - We do NOT support or tolerate anyone who wants to use this API to send spam
 *   or commit other online crimes.
 * - You will NOT use this API for marketing or other abusive purposes (spam,
 *   botting, harassment, massive bulk messaging...).
 *
 * @author mgp25: Founder, Reversing, Project Leader (https://github.com/mgp25)
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class Instagram implements ExperimentsInterface
{
    /**
     * Experiments refresh interval in sec.
     *
     * @var int
     */
    const EXPERIMENTS_REFRESH = 7200;

    /**
     * Currently active Instagram username.
     *
     * @var string
     */
    public $username;

    /**
     * Currently active Instagram password.
     *
     * @var string
     */
    public $password;

    /**
     * The Android device for the currently active user.
     *
     * @var \InstagramAPI\Devices\DeviceInterface
     */
    public $device;

    /**
     * Toggles API query/response debug output.
     *
     * @var bool
     */
    public $debug;

    /**
     * Toggles truncating long responses when debugging.
     *
     * @var bool
     */
    public $truncatedDebug;

    /**
     * For internal use by Instagram-API developers!
     *
     * Toggles the throwing of exceptions whenever Instagram-API's "Response"
     * classes lack fields that were provided by the server. Useful for
     * discovering that our library classes need updating.
     *
     * This is only settable via this public property and is NOT meant for
     * end-users of this library. It is for contributing developers!
     *
     * @var bool
     */
    public $apiDeveloperDebug = false;

    /**
     * Global flag for users who want to run the library incorrectly online.
     *
     * YOU ENABLE THIS AT YOUR OWN RISK! WE GIVE _ZERO_ SUPPORT FOR THIS MODE!
     * EMBEDDING THE LIBRARY DIRECTLY IN A WEBPAGE (WITHOUT ANY INTERMEDIARY
     * PROTECTIVE LAYER) CAN CAUSE ALL KINDS OF DAMAGE AND DATA CORRUPTION!
     *
     * YOU HAVE BEEN WARNED. ANY DATA CORRUPTION YOU CAUSE IS YOUR OWN PROBLEM!
     *
     * The right way to run the library online is described in `webwarning.htm`.
     *
     * @var bool
     *
     * @see Instagram::__construct()
     */
    public static $allowDangerousWebUsageAtMyOwnRisk = false;

    /**
     * UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * Google Play Advertising ID.
     *
     * The advertising ID is a unique ID for advertising, provided by Google
     * Play services for use in Google Play apps. Used by Instagram.
     *
     * @var string
     *
     * @see https://support.google.com/googleplay/android-developer/answer/6048248?hl=en
     */
    public $advertising_id;

    /**
     * Device ID.
     *
     * @var string
     */
    public $device_id;

    /**
     * Phone ID.
     *
     * @var string
     */
    public $phone_id;

    /**
     * Numerical UserPK ID of the active user account.
     *
     * @var string
     */
    public $account_id;

    /**
     * Our current guess about the session status.
     *
     * This contains our current GUESS about whether the current user is still
     * logged in. There is NO GUARANTEE that we're still logged in. For example,
     * the server may have invalidated our current session due to the account
     * password being changed AFTER our last login happened (which kills all
     * existing sessions for that account), or the session may have expired
     * naturally due to not being used for a long time, and so on...
     *
     * NOTE TO USERS: The only way to know for sure if you're logged in is to
     * try a request. If it throws a `LoginRequiredException`, then you're not
     * logged in anymore. The `login()` function will always ensure that your
     * current session is valid. But AFTER that, anything can happen... It's up
     * to Instagram, and we have NO control over what they do with your session!
     *
     * @var bool
     */
    public $isMaybeLoggedIn = false;

    /**
     * Raw API communication/networking class.
     *
     * @var Client
     */
    public $client;

    /**
     * The account settings storage.
     *
     * @var \InstagramAPI\Settings\StorageHandler|null
     */
    public $settings;

    /**
     * The current application session ID.
     *
     * This is a temporary ID which changes in the official app every time the
     * user closes and re-opens the Instagram application or switches account.
     *
     * @var string
     */
    public $session_id;

    /**
     * A list of experiments enabled on per-account basis.
     *
     * @var array
     */
    public $experiments;

    /** @var Request\Account Collection of Account related functions. */
    public $account;
    /** @var Request\Business Collection of Business related functions. */
    public $business;
    /** @var Request\Collection Collection of Collections related functions. */
    public $collection;
    /** @var Request\Creative Collection of Creative related functions. */
    public $creative;
    /** @var Request\Direct Collection of Direct related functions. */
    public $direct;
    /** @var Request\Discover Collection of Discover related functions. */
    public $discover;
    /** @var Request\Hashtag Collection of Hashtag related functions. */
    public $hashtag;
    /** @var Request\Highlight Collection of Highlight related functions. */
    public $highlight;
    /** @var Request\TV Collection of Instagram TV functions. */
    public $tv;
    /** @var Request\Internal Collection of Internal (non-public) functions. */
    public $internal;
    /** @var Request\Live Collection of Live related functions. */
    public $live;
    /** @var Request\Location Collection of Location related functions. */
    public $location;
    /** @var Request\Media Collection of Media related functions. */
    public $media;
    /** @var Request\People Collection of People related functions. */
    public $people;
    /** @var Request\Push Collection of Push related functions. */
    public $push;
    /** @var Request\Shopping Collection of Shopping related functions. */
    public $shopping;
    /** @var Request\Story Collection of Story related functions. */
    public $story;
    /** @var Request\Timeline Collection of Timeline related functions. */
    public $timeline;
    /** @var Request\Usertag Collection of Usertag related functions. */
    public $usertag;

    /**
     * Constructor.
     *
     * @param bool  $debug          Show API queries and responses.
     * @param bool  $truncatedDebug Truncate long responses in debug.
     * @param array $storageConfig  Configuration for the desired
     *                              user settings storage backend.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function __construct(
        $debug = false,
        $truncatedDebug = false,
        array $storageConfig = [])
    {
        // Disable incorrect web usage by default. People should never embed
        // this application emulator library directly in a webpage, or they
        // might cause all kinds of damage and data corruption. They should
        // use an intermediary layer such as a database or a permanent process!
        // NOTE: People can disable this safety via the flag at their own risk.
        if (!self::$allowDangerousWebUsageAtMyOwnRisk && (!defined('PHP_SAPI') || PHP_SAPI !== 'cli')) {
            // IMPORTANT: We do NOT throw any exception here for users who are
            // running the library via a webpage. Many webservers are configured
            // to hide all PHP errors, and would just give the user a totally
            // blank webpage with "Error 500" if we throw here, which would just
            // confuse the newbies even more. Instead, we output a HTML warning
            // message for people who try to run the library on a webpage.
            echo file_get_contents(__DIR__.'/../webwarning.htm');
            echo '<p>If you truly want to enable <em>incorrect</em> website usage by directly embedding this application emulator library in your page, then you can do that <strong>AT YOUR OWN RISK</strong> by setting the following flag <em>before</em> you create the <code>Instagram()</code> object:</p>'.PHP_EOL;
            echo '<p><code>\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;</code></p>'.PHP_EOL;
            exit(0); // Exit without error to avoid triggering Error 500.
        }

        // Prevent people from running this library on ancient PHP versions, and
        // verify that people have the most critically important PHP extensions.
        // NOTE: All of these are marked as requirements in composer.json, but
        // some people install the library at home and then move it somewhere
        // else without the requirements, and then blame us for their errors.
        if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50600) {
            throw new \InstagramAPI\Exception\InternalException(
                'You must have PHP 5.6 or higher to use the Instagram API library.'
            );
        }
        static $extensions = ['curl', 'mbstring', 'gd', 'exif', 'zlib'];
        foreach ($extensions as $ext) {
            if (!@extension_loaded($ext)) {
                throw new \InstagramAPI\Exception\InternalException(sprintf(
                    'You must have the "%s" PHP extension to use the Instagram API library.',
                    $ext
                ));
            }
        }

        // Debugging options.
        $this->debug = $debug;
        $this->truncatedDebug = $truncatedDebug;

        // Load all function collections.
        $this->account = new Request\Account($this);
        $this->business = new Request\Business($this);
        $this->collection = new Request\Collection($this);
        $this->creative = new Request\Creative($this);
        $this->direct = new Request\Direct($this);
        $this->discover = new Request\Discover($this);
        $this->hashtag = new Request\Hashtag($this);
        $this->highlight = new Request\Highlight($this);
        $this->tv = new Request\TV($this);
        $this->internal = new Request\Internal($this);
        $this->live = new Request\Live($this);
        $this->location = new Request\Location($this);
        $this->media = new Request\Media($this);
        $this->people = new Request\People($this);
        $this->push = new Request\Push($this);
        $this->shopping = new Request\Shopping($this);
        $this->story = new Request\Story($this);
        $this->timeline = new Request\Timeline($this);
        $this->usertag = new Request\Usertag($this);

        // Configure the settings storage and network client.
        $self = $this;
        $this->settings = Settings\Factory::createHandler(
            $storageConfig,
            [
                // This saves all user session cookies "in bulk" at script exit
                // or when switching to a different user, so that it only needs
                // to write cookies to storage a few times per user session:
                'onCloseUser' => function ($storage) use ($self) {
                    if ($self->client instanceof Client) {
                        $self->client->saveCookieJar();
                    }
                },
            ]
        );
        $this->client = new Client($this);
        $this->experiments = [];
    }

    /**
     * Controls the SSL verification behavior of the Client.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#verify
     *
     * @param bool|string $state TRUE to verify using PHP's default CA bundle,
     *                           FALSE to disable SSL verification (this is
     *                           insecure!), String to verify using this path to
     *                           a custom CA bundle file.
     */
    public function setVerifySSL(
        $state)
    {
        $this->client->setVerifySSL($state);
    }

    /**
     * Gets the current SSL verification behavior of the Client.
     *
     * @return bool|string
     */
    public function getVerifySSL()
    {
        return $this->client->getVerifySSL();
    }

    /**
     * Set the proxy to use for requests.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#proxy
     *
     * @param string|array|null $value String or Array specifying a proxy in
     *                                 Guzzle format, or NULL to disable
     *                                 proxying.
     */
    public function setProxy(
        $value)
    {
        $this->client->setProxy($value);
    }

    /**
     * Gets the current proxy used for requests.
     *
     * @return string|array|null
     */
    public function getProxy()
    {
        return $this->client->getProxy();
    }

    /**
     * Sets the network interface override to use.
     *
     * Only works if Guzzle is using the cURL backend. But that's
     * almost always the case, on most PHP installations.
     *
     * @see http://php.net/curl_setopt CURLOPT_INTERFACE
     *
     * @param string|null $value Interface name, IP address or hostname, or NULL
     *                           to disable override and let Guzzle use any
     *                           interface.
     */
    public function setOutputInterface(
        $value)
    {
        $this->client->setOutputInterface($value);
    }

    /**
     * Gets the current network interface override used for requests.
     *
     * @return string|null
     */
    public function getOutputInterface()
    {
        return $this->client->getOutputInterface();
    }

    /**
     * Login to Instagram or automatically resume and refresh previous session.
     *
     * Sets the active account for the class instance. You can call this
     * multiple times to switch between multiple Instagram accounts.
     *
     * WARNING: You MUST run this function EVERY time your script runs! It
     * handles automatic session resume and relogin and app session state
     * refresh and other absolutely *vital* things that are important if you
     * don't want to be banned from Instagram!
     *
     * WARNING: This function MAY return a CHALLENGE telling you that the
     * account needs two-factor login before letting you log in! Read the
     * two-factor login example to see how to handle that.
     *
     * @param string $username           Your Instagram username.
     *                                   You can also use your email or phone,
     *                                   but take in mind that they won't work
     *                                   when you have two factor auth enabled.
     * @param string $password           Your Instagram password.
     * @param int    $appRefreshInterval How frequently `login()` should act
     *                                   like an Instagram app that's been
     *                                   closed and reopened and needs to
     *                                   "refresh its state", by asking for
     *                                   extended account state details.
     *                                   Default: After `1800` seconds, meaning
     *                                   `30` minutes after the last
     *                                   state-refreshing `login()` call.
     *                                   This CANNOT be longer than `6` hours.
     *                                   Read `_sendLoginFlow()`! The shorter
     *                                   your delay is the BETTER. You may even
     *                                   want to set it to an even LOWER value
     *                                   than the default 30 minutes!
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LoginResponse|null A login response if a
     *                                                   full (re-)login
     *                                                   happens, otherwise
     *                                                   `NULL` if an existing
     *                                                   session is resumed.
     */
    public function login(
        $username,
        $password,
        $appRefreshInterval = 1800)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('You must provide a username and password to login().');
        }

        return $this->_login($username, $password, false, $appRefreshInterval);
    }

    /**
     * Internal login handler.
     *
     * @param string $username
     * @param string $password
     * @param bool   $forceLogin         Force login to Instagram instead of
     *                                   resuming previous session. Used
     *                                   internally to do a new, full relogin
     *                                   when we detect an expired/invalid
     *                                   previous session.
     * @param int    $appRefreshInterval
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LoginResponse|null
     *
     * @see Instagram::login() The public login handler with a full description.
     */
    protected function _login(
        $username,
        $password,
        $forceLogin = false,
        $appRefreshInterval = 1800)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('You must provide a username and password to _login().');
        }

        // Switch the currently active user/pass if the details are different.
        if ($this->username !== $username || $this->password !== $password) {
            $this->_setUser($username, $password);
        }

        // Perform a full relogin if necessary.
        if (!$this->isMaybeLoggedIn || $forceLogin) {
            $this->_sendPreLoginFlow();

            try {
                $response = $this->request('accounts/login/')
                    ->setNeedsAuth(false)
                    ->addPost('country_codes', '[{"country_code":"1","source":["default","sim"]}]')
                    ->addPost('phone_id', $this->phone_id)
                    ->addPost('_csrftoken', $this->client->getToken())
                    ->addPost('username', $this->username)
                    ->addPost('adid', $this->advertising_id)
                    ->addPost('guid', $this->uuid)
                    ->addPost('device_id', $this->device_id)
                    ->addPost('password', $this->password)
                    ->addPost('google_tokens', '[]')
                    ->addPost('login_attempt_count', 0)
                    ->getResponse(new Response\LoginResponse());
            } catch (\InstagramAPI\Exception\InstagramException $e) {
                if ($e->hasResponse() && $e->getResponse()->isTwoFactorRequired()) {
                    // Login failed because two-factor login is required.
                    // Return server response to tell user they need 2-factor.
                    return $e->getResponse();
                } else {
                    // Login failed for some other reason... Re-throw error.
                    throw $e;
                }
            }

            $this->_updateLoginState($response);

            $this->_sendLoginFlow(true, $appRefreshInterval);

            // Full (re-)login successfully completed. Return server response.
            return $response;
        }

        // Attempt to resume an existing session, or full re-login if necessary.
        // NOTE: The "return" here gives a LoginResponse in case of re-login.
        return $this->_sendLoginFlow(false, $appRefreshInterval);
    }

    /**
     * Finish a two-factor authenticated login.
     *
     * This function finishes a two-factor challenge that was provided by the
     * regular `login()` function. If you successfully answer their challenge,
     * you will be logged in after this function call.
     *
     * @param string $username            Your Instagram username used for logging
     * @param string $password            Your Instagram password.
     * @param string $twoFactorIdentifier Two factor identifier, obtained in
     *                                    login() response. Format: `123456`.
     * @param string $verificationCode    Verification code you have received
     *                                    via SMS.
     * @param string $verificationMethod  The verification method for 2FA. 1 is SMS,
     *                                    2 is backup codes and 3 is TOTP.
     * @param int    $appRefreshInterval  See `login()` for description of this
     *                                    parameter.
     * @param string $usernameHandler     Instagram username sent in the login response,
     *                                    Email and phone aren't allowed here.
     *                                    Default value is the first argument $username
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LoginResponse
     */
    public function finishTwoFactorLogin(
        $username,
        $password,
        $twoFactorIdentifier,
        $verificationCode,
        $verificationMethod = '1',
        $appRefreshInterval = 1800,
        $usernameHandler = null)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('You must provide a username and password to finishTwoFactorLogin().');
        }
        if (empty($verificationCode) || empty($twoFactorIdentifier)) {
            throw new \InvalidArgumentException('You must provide a verification code and two-factor identifier to finishTwoFactorLogin().');
        }
        if (!in_array($verificationMethod, ['1', '2', '3'], true)) {
            throw new \InvalidArgumentException('You must provide a valid verification method value.');
        }

        // Switch the currently active user/pass if the details are different.
        // NOTE: The username and password AREN'T actually necessary for THIS
        // endpoint, but this extra step helps people who statelessly embed the
        // library directly into a webpage, so they can `finishTwoFactorLogin()`
        // on their second page load without having to begin any new `login()`
        // call (since they did that in their previous webpage's library calls).
        if ($this->username !== $username || $this->password !== $password) {
            $this->_setUser($username, $password);
        }

        $username = ($usernameHandler !== null) ? $usernameHandler : $username;

        // Remove all whitespace from the verification code.
        $verificationCode = preg_replace('/\s+/', '', $verificationCode);

        $response = $this->request('accounts/two_factor_login/')
            ->setNeedsAuth(false)
            // 1 - SMS, 2 - Backup codes, 3 - TOTP, 0 - ??
            ->addPost('verification_method', $verificationMethod)
            ->addPost('verification_code', $verificationCode)
            ->addPost('two_factor_identifier', $twoFactorIdentifier)
            ->addPost('_csrftoken', $this->client->getToken())
            ->addPost('username', $username)
            ->addPost('device_id', $this->device_id)
            ->addPost('guid', $this->uuid)
            ->getResponse(new Response\LoginResponse());

        $this->_updateLoginState($response);

        $this->_sendLoginFlow(true, $appRefreshInterval);

        return $response;
    }

    /**
     * Request a new security code SMS for a Two Factor login account.
     *
     * NOTE: You should first attempt to `login()` which will automatically send
     * you a two factor SMS. This function is just for asking for a new SMS if
     * the old code has expired.
     *
     * NOTE: Instagram can only send you a new code every 60 seconds.
     *
     * @param string $username            Your Instagram username.
     * @param string $password            Your Instagram password.
     * @param string $twoFactorIdentifier Two factor identifier, obtained in
     *                                    `login()` response.
     * @param string $usernameHandler     Instagram username sent in the login response,
     *                                    Email and phone aren't allowed here.
     *                                    Default value is the first argument $username
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\TwoFactorLoginSMSResponse
     */
    public function sendTwoFactorLoginSMS(
        $username,
        $password,
        $twoFactorIdentifier,
        $usernameHandler = null)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('You must provide a username and password to sendTwoFactorLoginSMS().');
        }
        if (empty($twoFactorIdentifier)) {
            throw new \InvalidArgumentException('You must provide a two-factor identifier to sendTwoFactorLoginSMS().');
        }

        // Switch the currently active user/pass if the details are different.
        // NOTE: The password IS NOT actually necessary for THIS
        // endpoint, but this extra step helps people who statelessly embed the
        // library directly into a webpage, so they can `sendTwoFactorLoginSMS()`
        // on their second page load without having to begin any new `login()`
        // call (since they did that in their previous webpage's library calls).
        if ($this->username !== $username || $this->password !== $password) {
            $this->_setUser($username, $password);
        }

        $username = ($usernameHandler !== null) ? $usernameHandler : $username;

        return $this->request('accounts/send_two_factor_login_sms/')
            ->setNeedsAuth(false)
            ->addPost('two_factor_identifier', $twoFactorIdentifier)
            ->addPost('username', $username)
            ->addPost('device_id', $this->device_id)
            ->addPost('guid', $this->uuid)
            ->addPost('_csrftoken', $this->client->getToken())
            ->getResponse(new Response\TwoFactorLoginSMSResponse());
    }

    /**
     * Request information about available password recovery methods for an account.
     *
     * This will tell you things such as whether SMS or EMAIL-based recovery is
     * available for the given account name.
     *
     * `WARNING:` You can call this function without having called `login()`,
     * but be aware that a user database entry will be created for every
     * username you try to look up. This is ONLY meant for recovering your OWN
     * accounts.
     *
     * @param string $username Your Instagram username.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UsersLookupResponse
     */
    public function userLookup(
        $username)
    {
        // Set active user (without pwd), and create database entry if new user.
        $this->_setUserWithoutPassword($username);

        return $this->request('users/lookup/')
            ->setNeedsAuth(false)
            ->addPost('q', $username)
            ->addPost('directly_sign_in', true)
            ->addPost('username', $username)
            ->addPost('device_id', $this->device_id)
            ->addPost('guid', $this->uuid)
            ->addPost('_csrftoken', $this->client->getToken())
            ->getResponse(new Response\UsersLookupResponse());
    }

    /**
     * Request a recovery EMAIL to get back into your account.
     *
     * `WARNING:` You can call this function without having called `login()`,
     * but be aware that a user database entry will be created for every
     * username you try to look up. This is ONLY meant for recovering your OWN
     * accounts.
     *
     * @param string $username Your Instagram username.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\RecoveryResponse
     */
    public function sendRecoveryEmail(
        $username)
    {
        // Verify that they can use the recovery email option.
        $userLookup = $this->userLookup($username);
        if (!$userLookup->getCanEmailReset()) {
            throw new \InstagramAPI\Exception\InternalException('Email recovery is not available, since your account lacks a verified email address.');
        }

        return $this->request('accounts/send_recovery_flow_email/')
            ->setNeedsAuth(false)
            ->addPost('query', $username)
            ->addPost('adid', $this->advertising_id)
            ->addPost('device_id', $this->device_id)
            ->addPost('guid', $this->uuid)
            ->addPost('_csrftoken', $this->client->getToken())
            ->getResponse(new Response\RecoveryResponse());
    }

    /**
     * Request a recovery SMS to get back into your account.
     *
     * `WARNING:` You can call this function without having called `login()`,
     * but be aware that a user database entry will be created for every
     * username you try to look up. This is ONLY meant for recovering your OWN
     * accounts.
     *
     * @param string $username Your Instagram username.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\RecoveryResponse
     */
    public function sendRecoverySMS(
        $username)
    {
        // Verify that they can use the recovery SMS option.
        $userLookup = $this->userLookup($username);
        if (!$userLookup->getHasValidPhone() || !$userLookup->getCanSmsReset()) {
            throw new \InstagramAPI\Exception\InternalException('SMS recovery is not available, since your account lacks a verified phone number.');
        }

        return $this->request('users/lookup_phone/')
            ->setNeedsAuth(false)
            ->addPost('query', $username)
            ->addPost('_csrftoken', $this->client->getToken())
            ->getResponse(new Response\RecoveryResponse());
    }

    /**
     * Set the active account for the class instance.
     *
     * We can call this multiple times to switch between multiple accounts.
     *
     * @param string $username Your Instagram username.
     * @param string $password Your Instagram password.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     */
    protected function _setUser(
        $username,
        $password)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('You must provide a username and password to _setUser().');
        }

        // Load all settings from the storage and mark as current user.
        $this->settings->setActiveUser($username);

        // Generate the user's device instance, which will be created from the
        // user's last-used device IF they've got a valid, good one stored.
        // But if they've got a BAD/none, this will create a brand-new device.
        $savedDeviceString = $this->settings->get('devicestring');
        $this->device = new Devices\Device(
            Constants::IG_VERSION,
            Constants::VERSION_CODE,
            Constants::USER_AGENT_LOCALE,
            $savedDeviceString
        );

        // Get active device string so that we can compare it to any saved one.
        $deviceString = $this->device->getDeviceString();

        // Generate a brand-new device fingerprint if the device wasn't reused
        // from settings, OR if any of the stored fingerprints are missing.
        // NOTE: The regeneration when our device model changes is to avoid
        // dangerously reusing the "previous phone's" unique hardware IDs.
        // WARNING TO CONTRIBUTORS: Only add new parameter-checks here if they
        // are CRITICALLY important to the particular device. We don't want to
        // frivolously force the users to generate new device IDs constantly.
        $resetCookieJar = false;
        if ($deviceString !== $savedDeviceString // Brand new device, or missing
            || empty($this->settings->get('uuid')) // one of the critically...
            || empty($this->settings->get('phone_id')) // ...important device...
            || empty($this->settings->get('device_id'))) { // ...parameters.
            // Erase all previously stored device-specific settings and cookies.
            $this->settings->eraseDeviceSettings();

            // Save the chosen device string to settings.
            $this->settings->set('devicestring', $deviceString);

            // Generate hardware fingerprints for the new device.
            $this->settings->set('device_id', Signatures::generateDeviceId());
            $this->settings->set('phone_id', Signatures::generateUUID(true));
            $this->settings->set('uuid', Signatures::generateUUID(true));

            // Erase any stored account ID, to ensure that we detect ourselves
            // as logged-out. This will force a new relogin from the new device.
            $this->settings->set('account_id', '');

            // We'll also need to throw out all previous cookies.
            $resetCookieJar = true;
        }

        // Generate other missing values. These are for less critical parameters
        // that don't need to trigger a complete device reset like above. For
        // example, this is good for new parameters that Instagram introduces
        // over time, since those can be added one-by-one over time here without
        // needing to wipe/reset the whole device.
        if (empty($this->settings->get('advertising_id'))) {
            $this->settings->set('advertising_id', Signatures::generateUUID(true));
        }
        if (empty($this->settings->get('session_id'))) {
            $this->settings->set('session_id', Signatures::generateUUID(true));
        }

        // Store various important parameters for easy access.
        $this->username = $username;
        $this->password = $password;
        $this->uuid = $this->settings->get('uuid');
        $this->advertising_id = $this->settings->get('advertising_id');
        $this->device_id = $this->settings->get('device_id');
        $this->phone_id = $this->settings->get('phone_id');
        $this->session_id = $this->settings->get('session_id');
        $this->experiments = $this->settings->getExperiments();

        // Load the previous session details if we're possibly logged in.
        if (!$resetCookieJar && $this->settings->isMaybeLoggedIn()) {
            $this->isMaybeLoggedIn = true;
            $this->account_id = $this->settings->get('account_id');
        } else {
            $this->isMaybeLoggedIn = false;
            $this->account_id = null;
        }

        // Configures Client for current user AND updates isMaybeLoggedIn state
        // if it fails to load the expected cookies from the user's jar.
        // Must be done last here, so that isMaybeLoggedIn is properly updated!
        // NOTE: If we generated a new device we start a new cookie jar.
        $this->client->updateFromCurrentSettings($resetCookieJar);
    }

    /**
     * Set the active account for the class instance, without knowing password.
     *
     * This internal function is used by all unauthenticated pre-login functions
     * whenever they need to perform unauthenticated requests, such as looking
     * up a user's account recovery options.
     *
     * `WARNING:` A user database entry will be created for every username you
     * set as the active user, exactly like the normal `_setUser()` function.
     * This is necessary so that we generate a user-device and data storage for
     * each given username, which gives us necessary data such as a "device ID"
     * for the new user's virtual device, to use in various API-call parameters.
     *
     * `WARNING:` This function CANNOT be used for performing logins, since
     * Instagram will validate the password and will reject the missing
     * password. It is ONLY meant to be used for *RECOVERY* PRE-LOGIN calls that
     * need device parameters when the user DOESN'T KNOW their password yet.
     *
     * @param string $username Your Instagram username.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     */
    protected function _setUserWithoutPassword(
        $username)
    {
        if (empty($username) || !is_string($username)) {
            throw new \InvalidArgumentException('You must provide a username.');
        }

        // Switch the currently active user/pass if the username is different.
        // NOTE: Creates a user database (device) for the user if they're new!
        // NOTE: Because we don't know their password, we'll mark the user as
        // having "NOPASSWORD" as pwd. The user will fix that when/if they call
        // `login()` with the ACTUAL password, which will tell us what it is.
        // We CANNOT use an empty string since `_setUser()` will not allow that!
        // NOTE: If the user tries to look up themselves WHILE they are logged
        // in, we'll correctly NOT call `_setUser()` since they're already set.
        if ($this->username !== $username) {
            $this->_setUser($username, 'NOPASSWORD');
        }
    }

    /**
     * Updates the internal state after a successful login.
     *
     * @param Response\LoginResponse $response The login response.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     */
    protected function _updateLoginState(
        Response\LoginResponse $response)
    {
        // This check is just protection against accidental bugs. It makes sure
        // that we always call this function with a *successful* login response!
        if (!$response instanceof Response\LoginResponse
            || !$response->isOk()) {
            throw new \InvalidArgumentException('Invalid login response provided to _updateLoginState().');
        }

        $this->isMaybeLoggedIn = true;
        $this->account_id = $response->getLoggedInUser()->getPk();
        $this->settings->set('account_id', $this->account_id);
        $this->settings->set('last_login', time());
    }

    /**
     * Sends pre-login flow. This is required to emulate real device behavior.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     */
    protected function _sendPreLoginFlow()
    {
        // Reset zero rating rewrite rules.
        $this->client->zeroRating()->reset();
        // Calling this non-token API will put a csrftoken in our cookie
        // jar. We must do this before any functions that require a token.
        $this->internal->readMsisdnHeader('ig_select_app');
        $this->internal->syncDeviceFeatures(true);
        $this->internal->sendLauncherSync(true);
        $this->internal->logAttribution();
        // We must fetch new token here, because it updates rewrite rules.
        $this->internal->fetchZeroRatingToken();
        // It must be at the end, because it's called when a user taps on login input.
        $this->account->setContactPointPrefill('prefill');
    }

    /**
     * Registers available Push channels during the login flow.
     */
    protected function _registerPushChannels()
    {
        // Forcibly remove the stored token value if >24 hours old.
        // This prevents us from constantly re-registering the user's
        // "useless" token if they have stopped using the Push features.
        try {
            $lastFbnsToken = (int) $this->settings->get('last_fbns_token');
        } catch (\Exception $e) {
            $lastFbnsToken = null;
        }
        if (!$lastFbnsToken || $lastFbnsToken < strtotime('-24 hours')) {
            try {
                $this->settings->set('fbns_token', '');
            } catch (\Exception $e) {
                // Ignore storage errors.
            }

            return;
        }

        // Read our token from the storage.
        try {
            $fbnsToken = $this->settings->get('fbns_token');
        } catch (\Exception $e) {
            $fbnsToken = null;
        }
        if ($fbnsToken === null) {
            return;
        }

        // Register our last token since we had a fresh (age <24 hours) one,
        // or clear our stored token if we fail to register it again.
        try {
            $this->push->register('mqtt', $fbnsToken);
        } catch (\Exception $e) {
            try {
                $this->settings->set('fbns_token', '');
            } catch (\Exception $e) {
                // Ignore storage errors.
            }
        }
    }

    /**
     * Sends login flow. This is required to emulate real device behavior.
     *
     * @param bool $justLoggedIn       Whether we have just performed a full
     *                                 relogin (rather than doing a resume).
     * @param int  $appRefreshInterval See `login()` for description of this
     *                                 parameter.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LoginResponse|null A login response if a
     *                                                   full (re-)login is
     *                                                   needed during the login
     *                                                   flow attempt, otherwise
     *                                                   `NULL`.
     */
    protected function _sendLoginFlow(
        $justLoggedIn,
        $appRefreshInterval = 1800)
    {
        if (!is_int($appRefreshInterval) || $appRefreshInterval < 0) {
            throw new \InvalidArgumentException("Instagram's app state refresh interval must be a positive integer.");
        }
        if ($appRefreshInterval > 21600) {
            throw new \InvalidArgumentException("Instagram's app state refresh interval is NOT allowed to be higher than 6 hours, and the lower the better!");
        }

        // SUPER IMPORTANT:
        //
        // STOP trying to ask us to remove this code section!
        //
        // EVERY time the user presses their device's home button to leave the
        // app and then comes back to the app, Instagram does ALL of these things
        // to refresh its internal app state. We MUST emulate that perfectly,
        // otherwise Instagram will silently detect you as a "fake" client
        // after a while!
        //
        // You can configure the login's $appRefreshInterval in the function
        // parameter above, but you should keep it VERY frequent (definitely
        // NEVER longer than 6 hours), so that Instagram sees you as a real
        // client that keeps quitting and opening their app like a REAL user!
        //
        // Otherwise they WILL detect you as a bot and silently BLOCK features
        // or even ban you.
        //
        // You have been warned.
        if ($justLoggedIn) {
            // Reset zero rating rewrite rules.
            $this->client->zeroRating()->reset();
            // Perform the "user has just done a full login" API flow.
            $this->internal->sendLauncherSync(false);
            $this->internal->syncUserFeatures();
            $this->timeline->getTimelineFeed(null, ['recovered_from_crash' => true]);
            $this->story->getReelsTrayFeed();
            $this->discover->getSuggestedSearches('users');
            $this->discover->getRecentSearches();
            $this->discover->getSuggestedSearches('blended');
            //$this->story->getReelsMediaFeed();
            // We must fetch new token here, because it updates rewrite rules.
            $this->internal->fetchZeroRatingToken();
            $this->_registerPushChannels();
            $this->direct->getRankedRecipients('reshare', true);
            $this->direct->getRankedRecipients('raven', true);
            $this->direct->getInbox();
            //$this->internal->logResurrectAttribution();
            $this->direct->getPresences();
            $this->people->getRecentActivityInbox();
            $this->internal->getLoomFetchConfig();
            $this->internal->getProfileNotice();
            $this->media->getBlockedMedia();
            //$this->account->getProcessContactPointSignals();
            $this->people->getBootstrapUsers();
            //$this->internal->getQPCooldowns();
            $this->discover->getExploreFeed(null, true);
            //$this->internal->getMegaphoneLog();
            $this->internal->getQPFetch();
            $this->internal->getFacebookOTA();
        } else {
            $lastLoginTime = $this->settings->get('last_login');
            $isSessionExpired = $lastLoginTime === null || (time() - $lastLoginTime) > $appRefreshInterval;

            // Act like a real logged in app client refreshing its news timeline.
            // This also lets us detect if we're still logged in with a valid session.
            try {
                $this->timeline->getTimelineFeed(null, [
                    'is_pull_to_refresh' => $isSessionExpired ? null : mt_rand(1, 3) < 3,
                ]);
            } catch (\InstagramAPI\Exception\LoginRequiredException $e) {
                // If our session cookies are expired, we were now told to login,
                // so handle that by running a forced relogin in that case!
                return $this->_login($this->username, $this->password, true, $appRefreshInterval);
            }

            // Perform the "user has returned to their already-logged in app,
            // so refresh all feeds to check for news" API flow.
            if ($isSessionExpired) {
                $this->settings->set('last_login', time());

                // Generate and save a new application session ID.
                $this->session_id = Signatures::generateUUID(true);
                $this->settings->set('session_id', $this->session_id);

                // Do the rest of the "user is re-opening the app" API flow...
                $this->people->getBootstrapUsers();
                $this->story->getReelsTrayFeed();
                $this->direct->getRankedRecipients('reshare', true);
                $this->direct->getRankedRecipients('raven', true);
                $this->_registerPushChannels();
                //$this->internal->getMegaphoneLog();
                $this->direct->getInbox();
                $this->direct->getPresences();
                $this->people->getRecentActivityInbox();
                $this->internal->getProfileNotice();
                $this->discover->getExploreFeed();
            }

            // Users normally resume their sessions, meaning that their
            // experiments never get synced and updated. So sync periodically.
            $lastExperimentsTime = $this->settings->get('last_experiments');
            if ($lastExperimentsTime === null || (time() - $lastExperimentsTime) > self::EXPERIMENTS_REFRESH) {
                $this->internal->syncUserFeatures();
                $this->internal->syncDeviceFeatures();
            }

            // Update zero rating token when it has been expired.
            $expired = time() - (int) $this->settings->get('zr_expires');
            if ($expired > 0) {
                $this->client->zeroRating()->reset();
                $this->internal->fetchZeroRatingToken($expired > 7200 ? 'token_stale' : 'token_expired');
            }
        }

        // We've now performed a login or resumed a session. Forcibly write our
        // cookies to the storage, to ensure that the storage doesn't miss them
        // in case something bad happens to PHP after this moment.
        $this->client->saveCookieJar();
    }

    /**
     * Log out of Instagram.
     *
     * WARNING: Most people should NEVER call `logout()`! Our library emulates
     * the Instagram app for Android, where you are supposed to stay logged in
     * forever. By calling this function, you will tell Instagram that you are
     * logging out of the APP. But you SHOULDN'T do that! In almost 100% of all
     * cases you want to *stay logged in* so that `login()` resumes your session!
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LogoutResponse
     *
     * @see Instagram::login()
     */
    public function logout()
    {
        $response = $this->request('accounts/logout/')
            ->setSignedPost(false)
            ->addPost('phone_id', $this->phone_id)
            ->addPost('_csrftoken', $this->client->getToken())
            ->addPost('guid', $this->uuid)
            ->addPost('device_id', $this->device_id)
            ->addPost('_uuid', $this->uuid)
            ->getResponse(new Response\LogoutResponse());

        // We've now logged out. Forcibly write our cookies to the storage, to
        // ensure that the storage doesn't miss them in case something bad
        // happens to PHP after this moment.
        $this->client->saveCookieJar();

        return $response;
    }

    /**
     * Checks if a parameter is enabled in the given experiment.
     *
     * @param string $experiment
     * @param string $param
     * @param bool   $default
     *
     * @return bool
     */
    public function isExperimentEnabled(
        $experiment,
        $param,
        $default = false)
    {
        return isset($this->experiments[$experiment][$param])
            ? in_array($this->experiments[$experiment][$param], ['enabled', 'true', '1'])
            : $default;
    }

    /**
     * Get a parameter value for the given experiment.
     *
     * @param string $experiment
     * @param string $param
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getExperimentParam(
        $experiment,
        $param,
        $default = null)
    {
        return isset($this->experiments[$experiment][$param])
            ? $this->experiments[$experiment][$param]
            : $default;
    }

    /**
     * Create a custom API request.
     *
     * Used internally, but can also be used by end-users if they want
     * to create completely custom API queries without modifying this library.
     *
     * @param string $url
     *
     * @return \InstagramAPI\Request
     */
    public function request(
        $url)
    {
        return new Request($this, $url);
    }
}
