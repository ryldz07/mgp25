<?php

namespace InstagramAPI\Exception;

/**
 * Means that request start-line and/or headers are too large
 * to be processed by Instagram's server.
 */
class RequestHeadersTooLargeException extends RequestException
{
}
