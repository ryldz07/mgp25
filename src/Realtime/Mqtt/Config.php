<?php

namespace InstagramAPI\Realtime\Mqtt;

class Config
{
    /* MQTT server options */
    const DEFAULT_HOST = 'edge-mqtt.facebook.com';
    const DEFAULT_PORT = 443;

    /* MQTT protocol options */
    const MQTT_KEEPALIVE = 900;
    const MQTT_VERSION = 3;

    /* MQTT client options */
    const NETWORK_TYPE_WIFI = 1;
    const CLIENT_TYPE = 'cookie_auth';
    const PUBLISH_FORMAT = 'jz';
    const CONNECTION_TIMEOUT = 5;
}
