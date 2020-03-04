<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * UploadJobVideoResponse.
 *
 * @method mixed getMessage()
 * @method string getStatus()
 * @method string getUploadId()
 * @method Model\VideoUploadUrl[] getVideoUploadUrls()
 * @method Model\_Message[] get_Messages()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool isUploadId()
 * @method bool isVideoUploadUrls()
 * @method bool is_Messages()
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this setUploadId(string $value)
 * @method $this setVideoUploadUrls(Model\VideoUploadUrl[] $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unsetUploadId()
 * @method $this unsetVideoUploadUrls()
 * @method $this unset_Messages()
 */
class UploadJobVideoResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'upload_id'         => 'string',
        'video_upload_urls' => 'Model\VideoUploadUrl[]',
    ];
}
