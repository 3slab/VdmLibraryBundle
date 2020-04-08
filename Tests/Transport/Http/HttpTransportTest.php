<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Transport\Http;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\LibraryBundle\Executor\Http\DefaultHttpExecutor;
use Vdm\Bundle\LibraryBundle\Transport\Http\HttpTransport;

class HttpTransportTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $serializer
     */
    private $serializer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $httpClient
     */
    private $httpClient;

    /**
     * @var DefaultHttpExecutor $httpExecutor
     */
    private $httpExecutor;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->serializer = $this->getMockBuilder(\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface::class)->getMock();
        $this->httpClient = $this->getMockBuilder(\Symfony\Contracts\HttpClient\HttpClientInterface::class)->getMock();
        $this->httpExecutor = new DefaultHttpExecutor($this->logger, $this->serializer, $this->httpClient);
    }

    public function testGet()
    {
        $httpTransport= new HttpTransport($this->httpExecutor, "https://ipconfig.io/json", "GET", []);
        $array = $httpTransport->get();

        $this->assertEquals(\Symfony\Component\Messenger\Envelope::class, get_class($array->current()));
        $this->assertCount(1, $array);
    }

    public function testSend()
    {
        $httpTransport = $this
                ->getMockBuilder(HttpTransport::class)
                ->disableOriginalConstructor()
                ->setMethods(null)
                ->getMock();

        $this->expectException(\Exception::class);

        $envelope = new \Symfony\Component\Messenger\Envelope(new \Vdm\Bundle\LibraryBundle\Model\Message(""));
        $httpTransport->send($envelope);        
    }
}
