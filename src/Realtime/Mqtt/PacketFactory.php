<?php

/*
 * This file is part of net-mqtt.
 *
 * Copyright (c) 2015 Sebastian Mößler code@binsoul.de
 *
 * This source file is subject to the MIT license.
 */

namespace InstagramAPI\Realtime\Mqtt;

use BinSoul\Net\Mqtt\Exception\UnknownPacketTypeException;
use BinSoul\Net\Mqtt\Packet;
use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\ConnectResponsePacket;
use BinSoul\Net\Mqtt\Packet\DisconnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\PingRequestPacket;
use BinSoul\Net\Mqtt\Packet\PingResponsePacket;
use BinSoul\Net\Mqtt\Packet\PublishCompletePacket;
use BinSoul\Net\Mqtt\Packet\PublishReceivedPacket;
use BinSoul\Net\Mqtt\Packet\PublishReleasePacket;
use BinSoul\Net\Mqtt\Packet\PublishRequestPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket;
use Fbns\Client\Common\PublishAckPacket;

/**
 * Builds instances of the {@see Packet} interface.
 */
class PacketFactory
{
    /**
     * Map of packet types to packet classes.
     *
     * @var string[]
     */
    private static $_mapping = [
        Packet::TYPE_CONNECT     => ConnectRequestPacket::class,
        Packet::TYPE_CONNACK     => ConnectResponsePacket::class,
        Packet::TYPE_PUBLISH     => PublishRequestPacket::class,
        Packet::TYPE_PUBACK      => PublishAckPacket::class,
        Packet::TYPE_PUBREC      => PublishReceivedPacket::class,
        Packet::TYPE_PUBREL      => PublishReleasePacket::class,
        Packet::TYPE_PUBCOMP     => PublishCompletePacket::class,
        Packet::TYPE_SUBSCRIBE   => SubscribeRequestPacket::class,
        Packet::TYPE_SUBACK      => SubscribeResponsePacket::class,
        Packet::TYPE_UNSUBSCRIBE => UnsubscribeRequestPacket::class,
        Packet::TYPE_UNSUBACK    => UnsubscribeResponsePacket::class,
        Packet::TYPE_PINGREQ     => PingRequestPacket::class,
        Packet::TYPE_PINGRESP    => PingResponsePacket::class,
        Packet::TYPE_DISCONNECT  => DisconnectRequestPacket::class,
    ];

    /**
     * Builds a packet object for the given type.
     *
     * @param int $type
     *
     * @throws UnknownPacketTypeException
     *
     * @return Packet
     */
    public function build(
        $type)
    {
        if (!isset(self::$_mapping[$type])) {
            throw new UnknownPacketTypeException(sprintf('Unknown packet type %d.', $type));
        }

        $class = self::$_mapping[$type];

        return new $class();
    }
}
