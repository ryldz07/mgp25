<?php

namespace InstagramAPI\Exception;

/**
 * Used for endpoint calls that fail with HTTP code "400 Bad Request", but only
 * if no other more serious exception was found in the server response.
 */
class BadRequestException extends EndpointException
{
}
