<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FaceModelsResponse.
 *
 * @method Model\FaceModels getFaceModels()
 * @method mixed getMessage()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isFaceModels()
 * @method bool isMessage()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setFaceModels(Model\FaceModels $value)
 * @method $this setMessage(mixed $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetFaceModels()
 * @method $this unsetMessage()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class FaceModelsResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'face_models' => 'Model\FaceModels',
    ];
}
