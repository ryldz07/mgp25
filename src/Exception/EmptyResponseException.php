<?php

namespace InstagramAPI\Exception;

/**
 * Used for endpoint calls that returned an empty/invalid (non-JSON) response.
 */
class EmptyResponseException extends EndpointException
{
}
