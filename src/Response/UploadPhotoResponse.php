<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * UploadPhotoResponse.
 *
 * @method string getMediaId()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method string getUploadId()
 * @method Model\_Message[] get_Messages()
 * @method bool isMediaId()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isUploadId()
 * @method bool is_Messages()
 * @method $this setMediaId(string $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setUploadId(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMediaId()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetUploadId()
 * @method $this unset_Messages()
 */
class UploadPhotoResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'upload_id' => 'string',
        'media_id'  => 'string',
    ];
}
