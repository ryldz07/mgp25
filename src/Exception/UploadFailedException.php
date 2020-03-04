<?php

namespace InstagramAPI\Exception;

/**
 * Used when we know for a fact that our uploads failed.
 *
 * However, this is not the only type of exception used for failed uploads/API
 * communication. There can also be lower-level Guzzle HTTP exceptions.
 */
class UploadFailedException extends RequestException
{
}
