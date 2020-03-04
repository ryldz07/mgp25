<?php

namespace InstagramAPI\Tests\React;

use Clue\React\HttpProxy\ProxyConnector as HttpConnectProxy;
use Clue\React\Socks\Client as SocksProxy;
use InstagramAPI\Instagram;
use InstagramAPI\React\Connector;
use PHPUnit\Framework\TestCase;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectorInterface;
use React\Socket\SecureConnector;

class ConnectorTest extends TestCase
{
    const HOST = 'mqtt-edge.facebook.com';

    /**
     * @param object $object
     * @param string $method
     * @param array  ...$args
     *
     * @return mixed
     */
    protected function _callProtectedMethod(
        $object,
        $method,
        ...$args)
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($object, $args);
    }

    /**
     * @return Connector
     */
    protected function _createConnector()
    {
        /** @var Instagram $instagramMock */
        $instagramMock = $this->createMock(Instagram::class);
        /** @var LoopInterface $loopMock */
        $loopMock = $this->createMock(LoopInterface::class);

        return new Connector($instagramMock, $loopMock);
    }

    public function testEmptyProxyConfigShouldReturnNull()
    {
        $connector = $this->_createConnector();

        $this->assertEquals(
            null,
            $this->_callProtectedMethod($connector, '_getProxyForHost', self::HOST, null)
        );
    }

    public function testSingleProxyConfigShouldReturnAsIs()
    {
        $connector = $this->_createConnector();

        $this->assertEquals(
            '127.0.0.1:3128',
            $this->_callProtectedMethod($connector, '_getProxyForHost', self::HOST, '127.0.0.1:3128')
        );
    }

    public function testHttpProxyShouldThrow()
    {
        $connector = $this->_createConnector();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No proxy with CONNECT method found');
        $this->_callProtectedMethod($connector, '_getProxyForHost', self::HOST, [
            'http' => '127.0.0.1:3128',
        ]);
    }

    public function testMustPickHttpsProxy()
    {
        $connector = $this->_createConnector();

        $this->assertEquals(
            '127.0.0.1:3128',
            $this->_callProtectedMethod($connector, '_getProxyForHost', self::HOST, [
                'http'  => '127.0.0.1:3127',
                'https' => '127.0.0.1:3128',
            ])
        );
    }

    public function testShouldReturnNullWhenHostInExceptions()
    {
        $connector = $this->_createConnector();

        $this->assertEquals(
            null,
            $this->_callProtectedMethod($connector, '_getProxyForHost', self::HOST, [
                'https' => '127.0.0.1:3128',
                'no'    => ['.facebook.com'],
            ])
        );
    }

    public function testVerifyPeerEnabled()
    {
        $connector = $this->_createConnector();

        $context = $this->_callProtectedMethod($connector, '_getSecureContext', true);
        $this->assertInternalType('array', $context);
        $this->assertCount(3, $context);
        $this->assertArrayHasKey('verify_peer', $context);
        $this->assertEquals(true, $context['verify_peer']);
        $this->assertArrayHasKey('verify_peer_name', $context);
        $this->assertEquals(true, $context['verify_peer_name']);
        $this->assertArrayHasKey('allow_self_signed', $context);
        $this->assertEquals(false, $context['allow_self_signed']);
    }

    public function testVerifyPeerDisabled()
    {
        $connector = $this->_createConnector();

        $context = $this->_callProtectedMethod($connector, '_getSecureContext', false);
        $this->assertInternalType('array', $context);
        $this->assertCount(2, $context);
        $this->assertArrayHasKey('verify_peer', $context);
        $this->assertEquals(false, $context['verify_peer']);
        $this->assertArrayHasKey('verify_peer_name', $context);
        $this->assertEquals(false, $context['verify_peer_name']);
    }

    public function testVerifyPeerEnabledWithCustomCa()
    {
        $connector = $this->_createConnector();

        $context = $this->_callProtectedMethod($connector, '_getSecureContext', __FILE__);
        $this->assertInternalType('array', $context);
        $this->assertCount(4, $context);
        $this->assertArrayHasKey('cafile', $context);
        $this->assertEquals(__FILE__, $context['cafile']);
        $this->assertArrayHasKey('verify_peer', $context);
        $this->assertEquals(true, $context['verify_peer']);
        $this->assertArrayHasKey('verify_peer_name', $context);
        $this->assertEquals(true, $context['verify_peer_name']);
        $this->assertArrayHasKey('allow_self_signed', $context);
        $this->assertEquals(false, $context['allow_self_signed']);
    }

    public function testVerifyPeerEnabledWithCustomCaMissing()
    {
        $connector = $this->_createConnector();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CA bundle not found');
        $this->_callProtectedMethod($connector, '_getSecureContext', __FILE__.'.missing');
    }

    public function testVerifyPeerWithInvalidConfig()
    {
        $connector = $this->_createConnector();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid verify request option');
        $this->_callProtectedMethod($connector, '_getSecureContext', [__FILE__]);
    }

    public function testSecureConnector()
    {
        $connector = $this->_createConnector();

        $secureConnector = $this->_callProtectedMethod($connector, '_getSecureConnector', [], null);
        $this->assertInstanceOf(SecureConnector::class, $secureConnector);
    }

    public function testProxyWrappers()
    {
        $proxies = [
            'socks://127.0.0.1:1080'   => SocksProxy::class,
            'socks4://127.0.0.1:1080'  => SocksProxy::class,
            'socks4a://127.0.0.1:1080' => SocksProxy::class,
            'socks5://127.0.0.1:1080'  => SocksProxy::class,
            'http://127.0.0.1:3128'    => HttpConnectProxy::class,
            'https://127.0.0.1:3128'   => HttpConnectProxy::class,
            '127.0.0.1:3128'           => HttpConnectProxy::class,
        ];
        foreach ($proxies as $proxy => $targetClass) {
            $connector = $this->_createConnector();
            /** @var ConnectorInterface $baseConnector */
            $baseConnector = $this->createMock(ConnectorInterface::class);

            $this->assertInstanceOf(
                $targetClass,
                $this->_callProtectedMethod($connector, '_wrapConnectorIntoProxy', $baseConnector, $proxy)
            );
        }
    }

    public function testProxyWithoutWrapperShouldThrow()
    {
        $connector = $this->_createConnector();
        /** @var ConnectorInterface $baseConnector */
        $baseConnector = $this->createMock(ConnectorInterface::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported proxy scheme');
        $this->_callProtectedMethod($connector, '_wrapConnectorIntoProxy', $baseConnector, 'tcp://127.0.0.1');
    }
}
