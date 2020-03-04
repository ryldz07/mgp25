<?php

namespace InstagramAPI\Middleware;

use Psr\Http\Message\RequestInterface;

/**
 * Fake cookies middleware.
 *
 * This middleware sits between our class and Guzzle and gives us full access to
 * inject fake cookies into requests before speaking to Instagram's server.
 * Thus allowing us to perfectly emulate unusual Instagram API queries.
 *
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class FakeCookies
{
    /**
     * Injects fake cookies which aren't in our cookie jar.
     *
     * Fake cookies are only injected while this array is non-empty.
     *
     * @var array
     */
    private $_cookies;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->clear();
    }

    /**
     * Removes all fake cookies so they won't be added to further requests.
     */
    public function clear()
    {
        $this->_cookies = [];
    }

    /**
     * Get all currently used fake cookies.
     *
     * @return array
     */
    public function cookies()
    {
        return $this->_cookies;
    }

    /**
     * Adds a fake cookie which will be injected into all requests.
     *
     * Remember to clear your fake cookies when you no longer need them.
     *
     * Usually you only need fake cookies for a few requests, and care must be
     * taken to GUARANTEE clearout after that, via something like the following:
     * "try{...}finally{ ...->clearFakeCookies(); }").
     *
     * Otherwise you would FOREVER pollute all other requests!
     *
     * If you only need the cookie once, the best way to guarantee clearout is
     * to leave the "singleUse" parameter in its enabled state.
     *
     * @param string $name      The name of the cookie. CASE SENSITIVE!
     * @param string $value     The value of the cookie.
     * @param bool   $singleUse If TRUE, the cookie will be deleted after 1 use.
     */
    public function add(
        $name,
        $value,
        $singleUse = true)
    {
        // This overwrites any existing fake cookie with the same name, which is
        // intentional since the names of cookies must be unique.
        $this->_cookies[$name] = [
            'value'     => $value,
            'singleUse' => $singleUse,
        ];
    }

    /**
     * Delete a single fake cookie.
     *
     * Useful for selectively removing some fake cookies but keeping the rest.
     *
     * @param string $name The name of the cookie. CASE SENSITIVE!
     */
    public function delete(
        $name)
    {
        unset($this->_cookies[$name]);
    }

    /**
     * Middleware setup function.
     *
     * Called by Guzzle when it needs to add an instance of our middleware to
     * its stack. We simply return a callable which will process all requests.
     *
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(
        callable $handler)
    {
        return function (
            RequestInterface $request,
            array $options
        ) use ($handler) {
            $fakeCookies = $this->cookies();

            // Pass request through unmodified if no work to do (to save CPU).
            if (count($fakeCookies) === 0) {
                return $handler($request, $options);
            }

            $finalCookies = [];

            // Extract all existing cookies in this request's "Cookie:" header.
            if ($request->hasHeader('Cookie')) {
                $cookieHeaders = $request->getHeader('Cookie');
                foreach ($cookieHeaders as $headerLine) {
                    $theseCookies = explode('; ', $headerLine);
                    foreach ($theseCookies as $cookieEntry) {
                        $cookieParts = explode('=', $cookieEntry, 2);
                        if (count($cookieParts) == 2) {
                            // We have the name and value of the cookie!
                            $finalCookies[$cookieParts[0]] = $cookieParts[1];
                        } else {
                            // Unable to find an equals sign, just re-use this
                            // cookie as-is (TRUE="re-use literally").
                            $finalCookies[$cookieEntry] = true;
                        }
                    }
                }
            }

            // Inject all of our fake cookies, overwriting any name clashes.
            // NOTE: The name matching is CASE SENSITIVE!
            foreach ($fakeCookies as $name => $cookieInfo) {
                $finalCookies[$name] = $cookieInfo['value'];

                // Delete the cookie now if it was a single-use cookie.
                if ($cookieInfo['singleUse']) {
                    $this->delete($name);
                }
            }

            // Generate all individual cookie strings for the final cookies.
            $values = [];
            foreach ($finalCookies as $name => $value) {
                if ($value === true) {
                    // Cookies to re-use as-is, due to parsing error above.
                    $values[] = $name;
                } else {
                    $values[] = $name.'='.$value;
                }
            }

            // Generate our new, semicolon-separated "Cookie:" header line.
            // NOTE: This completely replaces the old header. As intended.
            $finalCookieHeader = implode('; ', $values);
            $request = $request->withHeader('Cookie', $finalCookieHeader);

            return $handler($request, $options);
        };
    }
}
