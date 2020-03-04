<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Owner.
 *
 * @method float getLat()
 * @method float getLng()
 * @method Location getLocationDict()
 * @method string getName()
 * @method string getPk()
 * @method string getProfilePicUrl()
 * @method string getProfilePicUsername()
 * @method string getShortName()
 * @method mixed getType()
 * @method bool isLat()
 * @method bool isLng()
 * @method bool isLocationDict()
 * @method bool isName()
 * @method bool isPk()
 * @method bool isProfilePicUrl()
 * @method bool isProfilePicUsername()
 * @method bool isShortName()
 * @method bool isType()
 * @method $this setLat(float $value)
 * @method $this setLng(float $value)
 * @method $this setLocationDict(Location $value)
 * @method $this setName(string $value)
 * @method $this setPk(string $value)
 * @method $this setProfilePicUrl(string $value)
 * @method $this setProfilePicUsername(string $value)
 * @method $this setShortName(string $value)
 * @method $this setType(mixed $value)
 * @method $this unsetLat()
 * @method $this unsetLng()
 * @method $this unsetLocationDict()
 * @method $this unsetName()
 * @method $this unsetPk()
 * @method $this unsetProfilePicUrl()
 * @method $this unsetProfilePicUsername()
 * @method $this unsetShortName()
 * @method $this unsetType()
 */
class Owner extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'type'                 => '',
        'pk'                   => 'string',
        'name'                 => 'string',
        'profile_pic_url'      => 'string',
        'profile_pic_username' => 'string',
        'short_name'           => 'string',
        'lat'                  => 'float',
        'lng'                  => 'float',
        'location_dict'        => 'Location',
    ];
}
