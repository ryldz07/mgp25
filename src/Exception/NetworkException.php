<?php

namespace InstagramAPI\Exception;

/**
 * This exception re-wraps ALL networking/socket exceptions.
 *
 * Currently (and probably forever) we're using the great Guzzle library for all
 * network communication. So this exception is used for re-wrapping their
 * exceptions into something derived from our own base InstagramException
 * instead, to make life much easier for our users (that way they only have
 * to catch our base exception in their projects, and won't have to also catch
 * Guzzle's various exceptions).
 *
 * It also ensures that users get a proper stack-trace showing which area of OUR
 * code threw the exception, instead of some useless line of GUZZLE'S code.
 *
 * The message we use is the same as the message of the Guzzle exception, but
 * nicely formatted to begin with "Network: " and to always end in punctuation
 * that forms complete English sentences, so that the exception message is ready
 * for display in user-facing applications.
 *
 * And if you (the programmer) want extra details that were included in Guzzle's
 * exception, you can simply use "getGuzzleException()" on this NetworkException
 * to get the original Guzzle exception instead, which has more networking
 * information, such as the failed HTTP request that was attempted.
 */
class NetworkException extends RequestException
{
    /** @var \Exception */
    private $_guzzleException;

    /**
     * Constructor.
     *
     * @param \Exception $guzzleException The original Guzzle exception.
     */
    public function __construct(
        \Exception $guzzleException)
    {
        $this->_guzzleException = $guzzleException;

        // Ensure that the message is nicely formatted and follows our standard.
        $message = 'Network: '.ServerMessageThrower::prettifyMessage($this->_guzzleException->getMessage());

        // Construct with our custom message.
        // NOTE: We DON'T assign the guzzleException to "$previous", otherwise
        // the user would still see something like "Uncaught GuzzleHttp\Exception\
        // RequestException" and Guzzle's stack trace, instead of "Uncaught
        // InstagramAPI\Exception\NetworkException" and OUR correct stack trace.
        parent::__construct($message);
    }

    /**
     * Gets the original Guzzle exception, which contains much more details.
     *
     * @return \Exception The original Guzzle exception.
     */
    public function getGuzzleException()
    {
        return $this->_guzzleException;
    }
}
