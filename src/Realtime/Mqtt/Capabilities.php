<?php

namespace InstagramAPI\Realtime\Mqtt;

class Capabilities
{
    const ACKNOWLEDGED_DELIVERY = 0;
    const PROCESSING_LASTACTIVE_PRESENCEINFO = 1;
    const EXACT_KEEPALIVE = 2;
    const REQUIRES_JSON_UNICODE_ESCAPES = 3;
    const DELTA_SENT_MESSAGE_ENABLED = 4;
    const USE_ENUM_TOPIC = 5;
    const SUPPRESS_GETDIFF_IN_CONNECT = 6;
    const USE_THRIFT_FOR_INBOX = 7;
    const USE_SEND_PINGRESP = 8;
    const REQUIRE_REPLAY_PROTECTION = 9;
    const DATA_SAVING_MODE = 10;
    const TYPING_OFF_WHEN_SENDING_MESSAGE = 11;

    const DEFAULT_SET = 0
        | 1 << self::ACKNOWLEDGED_DELIVERY
        | 1 << self::PROCESSING_LASTACTIVE_PRESENCEINFO
        | 1 << self::EXACT_KEEPALIVE
        | 0 << self::REQUIRES_JSON_UNICODE_ESCAPES
        | 1 << self::DELTA_SENT_MESSAGE_ENABLED
        | 1 << self::USE_ENUM_TOPIC
        | 0 << self::SUPPRESS_GETDIFF_IN_CONNECT
        | 1 << self::USE_THRIFT_FOR_INBOX
        | 1 << self::USE_SEND_PINGRESP
        | 0 << self::REQUIRE_REPLAY_PROTECTION
        | 0 << self::DATA_SAVING_MODE
        | 0 << self::TYPING_OFF_WHEN_SENDING_MESSAGE;
}
