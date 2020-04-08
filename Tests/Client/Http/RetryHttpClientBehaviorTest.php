<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Http;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Vdm\Bundle\LibraryBundle\Client\Http\RetryHttpClientBehavior;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Response\MockResponse;

class RetryHttpClientBehaviorTest extends TestCase
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
     * @var RetryHttpClientBehavior $retryHttpClient
     */
    private $retryHttpClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->httpClient = $this->getMockBuilder(\Symfony\Contracts\HttpClient\HttpClientInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();

        $this->retryHttpClient = new RetryHttpClientBehavior($this->logger, $this->httpClient, 5, 5);
    }

    public function testRequest()
    {
        $response = $this->retryHttpClient->request("GET", "https://ipconfig.io/json", []);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testRequestTransportException()
    {
        $retry = rand(1,4);

        $retryHttpClientException = new RetryHttpClientBehavior($this->logger, $this->httpClient, $retry-1, 1);

        $exception = new TransportException();
        $this->httpClient->expects($this->exactly($retry))->method('request')->willThrowException($exception);
        $this->expectException(TransportException::class);
        $retryHttpClientException->request("GET", "https://ipconfig.io/json", []);
    }

    public function testRequestServerException()
    {
        $retry = rand(1,4);

        $retryHttpClientException = new RetryHttpClientBehavior($this->logger, $this->httpClient, $retry-1, 1);

        $exception = new ServerException(new MockResponse(''));
        $this->httpClient->expects($this->exactly($retry))->method('request')->willThrowException($exception);
        $this->expectException(ServerException::class);
        $retryHttpClientException->request("GET", "https://ipconfig.io/json", []);
    }

    public function testRequestClientException()
    {
        $retry = rand(1,4);

        $retryHttpClientException = new RetryHttpClientBehavior($this->logger, $this->httpClient, $retry-1, 1);

        $exception = new ClientException(new MockResponse(''));
        $this->httpClient->expects($this->exactly($retry))->method('request')->willThrowException($exception);
        $this->expectException(ClientException::class);
        $retryHttpClientException->request("GET", "https://ipconfig.io/json", []);
    }
}
