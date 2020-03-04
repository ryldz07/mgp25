<?php

namespace InstagramAPI\Middleware;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

/**
 * Zero rating rewrite middleware.
 */
class ZeroRating
{
    /**
     * Default rewrite rules.
     *
     * @var array
     */
    const DEFAULT_REWRITE = [
        '^(https?:\/\/)(i)(\.instagram\.com/.*)$' => '$1b.$2$3',
    ];

    /**
     * Rewrite rules.
     *
     * @var array
     */
    private $_rules;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Reset rules to default ones.
     */
    public function reset()
    {
        $this->update(self::DEFAULT_REWRITE);
    }

    /**
     * Update rules.
     *
     * @param array $rules
     */
    public function update(
        array $rules = [])
    {
        $this->_rules = [];
        foreach ($rules as $from => $to) {
            $regex = "#{$from}#";
            $test = @preg_match($regex, '');
            if ($test === false) {
                continue;
            }
            $this->_rules[$regex] = strtr($to, [
                '\.' => '.',
            ]);
        }
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
            if (empty($this->_rules)) {
                return $handler($request, $options);
            }

            $oldUri = (string) $request->getUri();
            $uri = $this->rewrite($oldUri);
            if ($uri !== $oldUri) {
                $request = $request->withUri(new Uri($uri));
            }

            return $handler($request, $options);
        };
    }

    /**
     * Do a rewrite.
     *
     * @param string $uri
     *
     * @return string
     */
    public function rewrite(
        $uri)
    {
        foreach ($this->_rules as $from => $to) {
            $result = @preg_replace($from, $to, $uri);
            if (!is_string($result)) {
                continue;
            }
            // We must break at the first succeeded replace.
            if ($result !== $uri) {
                return $result;
            }
        }

        return $uri;
    }
}
