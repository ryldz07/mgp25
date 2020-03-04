<?php

namespace InstagramAPI;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use RuntimeException;

/**
 * Core class for Instagram API responses.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Response\Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Response\Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class Response extends AutoPropertyMapper
{
    /** @var string */
    const STATUS_OK = 'ok';
    /** @var string */
    const STATUS_FAIL = 'fail';

    const JSON_PROPERTY_MAP = [
        /*
         * Whether the API request succeeded or not.
         *
         * Can be: "ok", "fail".
         */
        'status'  => 'string',
        /*
         * Instagram's API failure error message(s).
         *
         * NOTE: This MUST be marked as 'mixed' since the server can give us
         * either a single string OR a data structure with multiple messages.
         * Our custom `getMessage()` will take care of parsing their value.
         */
        'message' => 'mixed',
        /*
         * This can exist in any Instagram API response, and carries special
         * status information.
         *
         * Known messages: "fb_needs_reauth", "vkontakte_needs_reauth",
         * "twitter_needs_reauth", "ameba_needs_reauth", "update_push_token".
         */
        '_messages' => 'Response\Model\_Message[]',
    ];

    /** @var HttpResponseInterface */
    public $httpResponse;

    /**
     * Checks if the response was successful.
     *
     * @return bool
     */
    public function isOk()
    {
        return $this->_getProperty('status') === self::STATUS_OK;
    }

    /**
     * Gets the message.
     *
     * This function overrides the normal getter with some special processing
     * to handle unusual multi-error message values in certain responses.
     *
     * @throws RuntimeException If the message object is of an unsupported type.
     *
     * @return string|null A message string if one exists, otherwise NULL.
     */
    public function getMessage()
    {
        // Instagram's API usually returns a simple error string. But in some
        // cases, they instead return a subarray of individual errors, in case
        // of APIs that can return multiple errors at once.
        //
        // Uncomment this if you want to test multiple error handling:
        // $json = '{"status":"fail","message":{"errors":["Select a valid choice. 0 is not one of the available choices."]}}';
        // $json = '{"status":"fail","message":{"errors":["Select a valid choice. 0 is not one of the available choices.","Another error.","One more error."]}}';
        // $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
        // $this->_setProperty('message', $data['message']);

        $message = $this->_getProperty('message');
        if ($message === null || is_string($message)) {
            // Single error string or nothing at all.
            return $message;
        } elseif (is_array($message)) {
            // Multiple errors in an "errors" subarray.
            if (count($message) === 1 && isset($message['errors']) && is_array($message['errors'])) {
                // Add "Multiple Errors" prefix if the response contains more than one.
                // But most of the time, there will only be one error in the array.
                $str = (count($message['errors']) > 1 ? 'Multiple Errors: ' : '');
                $str .= implode(' AND ', $message['errors']); // Assumes all errors are strings.
                return $str;
            } else {
                throw new RuntimeException('Unknown message object. Expected errors subarray but found something else. Please submit a ticket about needing an Instagram-API library update!');
            }
        } else {
            throw new RuntimeException('Unknown message type. Please submit a ticket about needing an Instagram-API library update!');
        }
    }

    /**
     * Gets the HTTP response.
     *
     * @return HttpResponseInterface
     */
    public function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * Sets the HTTP response.
     *
     * @param HttpResponseInterface $response
     */
    public function setHttpResponse(
        HttpResponseInterface $response)
    {
        $this->httpResponse = $response;
    }

    /**
     * Checks if an HTTP response value exists.
     *
     * @return bool
     */
    public function isHttpResponse()
    {
        return $this->httpResponse !== null;
    }
}
