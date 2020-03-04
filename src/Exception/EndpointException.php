<?php

namespace InstagramAPI\Exception;

/**
 * All general API function call "server response" failures use this exception.
 *
 * The server-side Instagram API functions are called "endpoints", and they all
 * perform very different tasks and have hundreds of different function-specific
 * error messages (which may change at any time). Instead of us trying to handle
 * all of them (which would be slow and stupid), we throw them to the user as an
 * EndpointException, so that it's up to the users to handle any specific error
 * strings they want to catch in their own projects.
 *
 * Encapsulates ALL function-call specific problems such as "User not found"
 * and so on. To see what happened, simply getMessage() on this exception, or
 * use hasResponse() and getResponse() to get the full server response.
 */
class EndpointException extends RequestException
{
}
