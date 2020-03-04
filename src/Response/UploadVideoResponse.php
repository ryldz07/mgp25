<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * UploadVideoResponse.
 *
 * @method float getConfigureDelayMs()
 * @method mixed getMessage()
 * @method mixed getResult()
 * @method string getStatus()
 * @method string getUploadId()
 * @method Model\_Message[] get_Messages()
 * @method bool isConfigureDelayMs()
 * @method bool isMessage()
 * @method bool isResult()
 * @method bool isStatus()
 * @method bool isUploadId()
 * @method bool is_Messages()
 * @method $this setConfigureDelayMs(float $value)
 * @method $this setMessage(mixed $value)
 * @method $this setResult(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setUploadId(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetConfigureDelayMs()
 * @method $this unsetMessage()
 * @method $this unsetResult()
 * @method $this unsetStatus()
 * @method $this unsetUploadId()
 * @method $this unset_Messages()
 */
class UploadVideoResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'upload_id'          => 'string',
        'configure_delay_ms' => 'float',
        'result'             => '',
    ];
}
