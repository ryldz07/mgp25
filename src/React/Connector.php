<?php

namespace InstagramAPI\React;

use Clue\React\HttpProxy\ProxyConnector as HttpConnectProxy;
use Clue\React\Socks\Client as SocksProxy;
use GuzzleHttp\Psr7\Uri;
use InstagramAPI\Instagram;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\Promise\RejectedPromise;
use React\Socket\Connector as SocketConnector;
use React\Socket\ConnectorInterface;
use React\Socket\SecureConnector;

class Connector implements ConnectorInterface
{
    /**
     * @var Instagram
     */
    protected $_instagram;

    /**
     * @var LoopInterface
     */
    protected $_loop;

    /**
     * @var ConnectorInterface[]
     */
    protected $_connectors;

    /**
     * Constructor.
     *
     * @param Instagram     $instagram
     * @param LoopInterface $loop
     */
    public function __construct(
        Instagram $instagram,
        LoopInterface $loop)
    {
        $this->_instagram = $instagram;
        $this->_loop = $loop;

        $this->_connectors = [];
    }

    /** {@inheritdoc} */
    public function connect(
        $uri)
    {
        $uriObj = new Uri($uri);
        $host = $this->_instagram->client->zeroRating()->rewrite($uriObj->getHost());
        $uriObj = $uriObj->withHost($host);
        if (!isset($this->_connectors[$host])) {
            try {
                $this->_connectors[$host] = $this->_getSecureConnector(
                    $this->_getSecureContext($this->_instagram->getVerifySSL()),
                    $this->_getProxyForHost($host, $this->_instagram->getProxy())
                );
            } catch (\Exception $e) {
                return new RejectedPromise($e);
            }
        }
        $niceUri = ltrim((string) $uriObj, '/');
        /** @var PromiseInterface $promise */
        $promise = $this->_connectors[$host]->connect($niceUri);

        return $promise;
    }

    /**
     * Create a secure connector for given configuration.
     *
     * @param array       $secureContext
     * @param string|null $proxyAddress
     *
     * @throws \InvalidArgumentException
     *
     * @return ConnectorInterface
     */
    protected function _getSecureConnector(
        array $secureContext = [],
        $proxyAddress = null)
    {
        $connector = new SocketConnector($this->_loop, [
            'tcp'     => true,
            'tls'     => false,
            'unix'    => false,
            'dns'     => true,
            'timeout' => true,
        ]);

        if ($proxyAddress !== null) {
            $connector = $this->_wrapConnectorIntoProxy($connector, $proxyAddress, $secureContext);
        }

        return new SecureConnector($connector, $this->_loop, $secureContext);
    }

    /**
     * Get a proxy address (if any) for the host based on the proxy config.
     *
     * @param string $host        Host.
     * @param mixed  $proxyConfig Proxy config.
     *
     * @throws \InvalidArgumentException
     *
     * @return string|null
     *
     * @see http://docs.guzzlephp.org/en/stable/request-options.html#proxy
     */
    protected function _getProxyForHost(
        $host,
        $proxyConfig = null)
    {
        // Empty config => no proxy.
        if (empty($proxyConfig)) {
            return;
        }

        // Plain string => return it.
        if (!is_array($proxyConfig)) {
            return $proxyConfig;
        }

        // HTTP proxies do not have CONNECT method.
        if (!isset($proxyConfig['https'])) {
            throw new \InvalidArgumentException('No proxy with CONNECT method found.');
        }

        // Check exceptions.
        if (isset($proxyConfig['no']) && \GuzzleHttp\is_host_in_noproxy($host, $proxyConfig['no'])) {
            return;
        }

        return $proxyConfig['https'];
    }

    /**
     * Parse given SSL certificate verification and return a secure context.
     *
     * @param mixed $config
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return array
     *
     * @see http://docs.guzzlephp.org/en/stable/request-options.html#verify
     */
    protected function _getSecureContext(
        $config)
    {
        $context = [];
        if ($config === true) {
            // PHP 5.6 or greater will find the system cert by default. When
            // < 5.6, use the Guzzle bundled cacert.
            if (PHP_VERSION_ID < 50600) {
                $context['cafile'] = \GuzzleHttp\default_ca_bundle();
            }
        } elseif (is_string($config)) {
            $context['cafile'] = $config;
            if (!is_file($config)) {
                throw new \RuntimeException(sprintf('SSL CA bundle not found: "%s".', $config));
            }
        } elseif ($config === false) {
            $context['verify_peer'] = false;
            $context['verify_peer_name'] = false;

            return $context;
        } else {
            throw new \InvalidArgumentException('Invalid verify request option.');
        }
        $context['verify_peer'] = true;
        $context['verify_peer_name'] = true;
        $context['allow_self_signed'] = false;

        return $context;
    }

    /**
     * Wrap the connector into a proxy one for given configuration.
     *
     * @param ConnectorInterface $connector
     * @param string             $proxyAddress
     * @param array              $secureContext
     *
     * @throws \InvalidArgumentException
     *
     * @return ConnectorInterface
     */
    protected function _wrapConnectorIntoProxy(
        ConnectorInterface $connector,
        $proxyAddress,
        array $secureContext = [])
    {
        if (strpos($proxyAddress, '://') === false) {
            $scheme = 'http';
        } else {
            $scheme = parse_url($proxyAddress, PHP_URL_SCHEME);
        }
        switch ($scheme) {
            case 'socks':
            case 'socks4':
            case 'socks4a':
            case 'socks5':
                $connector = new SocksProxy($proxyAddress, $connector);
                break;
            case 'http':
                $connector = new HttpConnectProxy($proxyAddress, $connector);
                break;
            case 'https':
                $connector = new HttpConnectProxy($proxyAddress, new SecureConnector($connector, $this->_loop, $secureContext));
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unsupported proxy scheme: "%s".', $scheme));
        }

        return $connector;
    }
}
