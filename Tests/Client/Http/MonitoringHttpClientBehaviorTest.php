<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Http;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Vdm\Bundle\LibraryBundle\Client\Http\MonitoringHttpClientBehavior;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Response\MockResponse;

class MonitoringHttpClientBehaviorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $httpClient
     */
    private $httpClient;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var MonitoringHttpClientBehavior $monitoringHttpClient
     */
    private $monitoringHttpClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->httpClient = $this->getMockBuilder(\Symfony\Contracts\HttpClient\HttpClientInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();

        $this->monitoringHttpClient = new MonitoringHttpClientBehavior($this->logger, $this->httpClient, $this->eventDispatcher);
    }

    public function testRequest()
    {
        $response = $this->monitoringHttpClient->request("GET", "https://ipconfig.io/json", []);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testRequestTransportException()
    {
        $this->httpClient->method('request')->willThrowException(new \Symfony\Component\HttpClient\Exception\TransportException());
        $this->expectException(\Symfony\Component\HttpClient\Exception\TransportException::class);
        $this->monitoringHttpClient->request("GET", "https://ipconfig.io/json", []);
    }

    public function testRequestServerException()
    {
        $exception = new ServerException(new MockResponse(''));
        $this->httpClient->method('request')->willThrowException($exception);
        $this->expectException(ServerException::class);
        $this->monitoringHttpClient->request("GET", "https://ipconfig.io/json", []);
        $this->eventDispatcher->expects($this->once())->method('dispatch');
    }

    public function testRequestClientException()
    {
        $exception = new ClientException(new MockResponse(''));
        $this->httpClient->method('request')->willThrowException($exception);
        $this->expectException(ClientException::class);
        $this->monitoringHttpClient->request("GET", "https://ipconfig.io/json", []);
        $this->eventDispatcher->expects($this->once())->method('dispatch');
    }

    public function testRequestException()
    {
        $this->httpClient->method('request')->willThrowException(new \Exception());
        $this->expectException(\Exception::class);
        $this->monitoringHttpClient->request("GET", "https://ipconfig.io/json", []);
    }
}