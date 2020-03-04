<?php

namespace InstagramAPI\Realtime\Mqtt;

class Topics
{
    const PUBSUB = '/pubsub';
    const PUBSUB_ID = '88';

    const SEND_MESSAGE = '/ig_send_message';
    const SEND_MESSAGE_ID = '132';

    const SEND_MESSAGE_RESPONSE = '/ig_send_message_response';
    const SEND_MESSAGE_RESPONSE_ID = '133';

    const IRIS_SUB = '/ig_sub_iris';
    const IRIS_SUB_ID = '134';

    const IRIS_SUB_RESPONSE = '/ig_sub_iris_response';
    const IRIS_SUB_RESPONSE_ID = '135';

    const MESSAGE_SYNC = '/ig_message_sync';
    const MESSAGE_SYNC_ID = '146';

    const REALTIME_SUB = '/ig_realtime_sub';
    const REALTIME_SUB_ID = '149';

    const GRAPHQL = '/graphql';
    const GRAPHQL_ID = '9';

    const REGION_HINT = '/t_region_hint';
    const REGION_HINT_ID = '150';

    const ID_TO_TOPIC_MAP = [
        self::PUBSUB_ID                => self::PUBSUB,
        self::SEND_MESSAGE_ID          => self::SEND_MESSAGE,
        self::SEND_MESSAGE_RESPONSE_ID => self::SEND_MESSAGE_RESPONSE,
        self::IRIS_SUB_ID              => self::IRIS_SUB,
        self::IRIS_SUB_RESPONSE_ID     => self::IRIS_SUB_RESPONSE,
        self::MESSAGE_SYNC_ID          => self::MESSAGE_SYNC,
        self::REALTIME_SUB_ID          => self::REALTIME_SUB,
        self::GRAPHQL_ID               => self::GRAPHQL,
        self::REGION_HINT_ID           => self::REGION_HINT,
    ];

    const TOPIC_TO_ID_MAP = [
        self::PUBSUB                => self::PUBSUB_ID,
        self::SEND_MESSAGE          => self::SEND_MESSAGE_ID,
        self::SEND_MESSAGE_RESPONSE => self::SEND_MESSAGE_RESPONSE_ID,
        self::IRIS_SUB              => self::IRIS_SUB_ID,
        self::IRIS_SUB_RESPONSE     => self::IRIS_SUB_RESPONSE_ID,
        self::MESSAGE_SYNC          => self::MESSAGE_SYNC_ID,
        self::REALTIME_SUB          => self::REALTIME_SUB_ID,
        self::GRAPHQL               => self::GRAPHQL_ID,
        self::REGION_HINT           => self::REGION_HINT_ID,
    ];
}
