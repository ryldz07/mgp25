<?php

namespace InstagramAPI\Push;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use Fbns\Client\AuthInterface;
use Fbns\Client\Connection;
use Fbns\Client\Lite;
use Fbns\Client\Message\Push as PushMessage;
use Fbns\Client\Message\Register;
use InstagramAPI\Constants;
use InstagramAPI\Devices\DeviceInterface;
use InstagramAPI\React\PersistentInterface;
use InstagramAPI\React\PersistentTrait;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectorInterface;

/**
 * The following events are emitted:
 *  - token - New PUSH token has been received.
 *  - push - New PUSH notification has been received.
 */
class Fbns implements PersistentInterface, EventEmitterInterface
{
    use PersistentTrait;
    use EventEmitterTrait;

    const CONNECTION_TIMEOUT = 5;

    const DEFAULT_HOST = 'mqtt-mini.facebook.com';
    const DEFAULT_PORT = 443;

    /** @var EventEmitterInterface */
    protected $_target;

    /** @var ConnectorInterface */
    protected $_connector;

    /** @var AuthInterface */
    protected $_auth;

    /** @var DeviceInterface */
    protected $_device;

    /** @var LoopInterface */
    protected $_loop;

    /** @var Lite */
    protected $_client;

    /** @var LoggerInterface */
    protected $_logger;

    /** @var bool */
    protected $_isActive;

    /**
     * Fbns constructor.
     *
     * @param EventEmitterInterface $target
     * @param ConnectorInterface    $connector
     * @param AuthInterface         $auth
     * @param DeviceInterface       $device
     * @param LoopInterface         $loop
     * @param LoggerInterface       $logger
     */
    public function __construct(
        EventEmitterInterface $target,
        ConnectorInterface $connector,
        AuthInterface $auth,
        DeviceInterface $device,
        LoopInterface $loop,
        LoggerInterface $logger)
    {
        $this->_target = $target;
        $this->_connector = $connector;
        $this->_auth = $auth;
        $this->_device = $device;
        $this->_loop = $loop;
        $this->_logger = $logger;

        $this->_client = $this->_getClient();
    }

    /**
     * Create a new FBNS client instance.
     *
     * @return Lite
     */
    protected function _getClient()
    {
        $client = new Lite($this->_loop, $this->_connector, $this->_logger);

        // Bind events.
        $client
            ->on('connect', function (Lite\ConnectResponsePacket $responsePacket) {
                // Update auth credentials.
                $authJson = $responsePacket->getAuth();
                if (strlen($authJson)) {
                    $this->_logger->info('Received a non-empty auth.', [$authJson]);
                    $this->emit('fbns_auth', [$authJson]);
                }

                // Register an application.
                $this->_client->register(Constants::PACKAGE_NAME, Constants::FACEBOOK_ANALYTICS_APPLICATION_ID);
            })
            ->on('disconnect', function () {
                // Try to reconnect.
                if (!$this->_reconnectInterval) {
                    $this->_connect();
                }
            })
            ->on('register', function (Register $message) {
                if (!empty($message->getError())) {
                    $this->_target->emit('error', [new \RuntimeException($message->getError())]);

                    return;
                }
                $this->_logger->info('Received a non-empty token.', [$message->getToken()]);
                $this->emit('fbns_token', [$message->getToken()]);
            })
            ->on('push', function (PushMessage $message) {
                $payload = $message->getPayload();

                try {
                    $notification = new Notification($payload);
                } catch (\Exception $e) {
                    $this->_logger->error(sprintf('Failed to decode push: %s', $e->getMessage()), [$payload]);

                    return;
                }
                $this->emit('push', [$notification]);
            });

        return $client;
    }

    /**
     * Try to establish a connection.
     */
    protected function _connect()
    {
        $this->_setReconnectTimer(function () {
            $connection = new Connection(
                $this->_auth,
                $this->_device->getFbUserAgent(Constants::FBNS_APPLICATION_NAME)
            );

            return $this->_client->connect(self::DEFAULT_HOST, self::DEFAULT_PORT, $connection, self::CONNECTION_TIMEOUT);
        });
    }

    /**
     * Start Push receiver.
     */
    public function start()
    {
        $this->_logger->info('Starting FBNS client...');
        $this->_isActive = true;
        $this->_reconnectInterval = 0;
        $this->_connect();
    }

    /**
     * Stop Push receiver.
     */
    public function stop()
    {
        $this->_logger->info('Stopping FBNS client...');
        $this->_isActive = false;
        $this->_cancelReconnectTimer();
        $this->_client->disconnect();
    }

    /** {@inheritdoc} */
    public function isActive()
    {
        return $this->_isActive;
    }

    /** {@inheritdoc} */
    public function getLogger()
    {
        return $this->_logger;
    }

    /** {@inheritdoc} */
    public function getLoop()
    {
        return $this->_loop;
    }
}
