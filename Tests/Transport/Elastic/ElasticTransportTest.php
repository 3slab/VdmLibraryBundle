<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Elastic;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClient;
use Vdm\Bundle\LibraryBundle\Model\Message;

class ElasticTransportTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var ElasticClient $elasticClient
     */
    private $elasticClient;

    /**
     * @var ElasticTransport $elasticTransport
     */
    private $elasticTransport;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->elasticClient = $this->getMockBuilder(ElasticClient::class)->disableOriginalConstructor()->getMock();
        $this->elasticTransport = new ElasticTransport($this->logger, $this->elasticClient, "elasticsearch://localhost:9200", ['index' => 'test']);
    }

    public function testSend()
    {
        $envelope = new Envelope(new Message('test'));

        $envelopeResponse = $this->elasticTransport->send($envelope);

        $this->assertInstanceOf(Envelope::class, $envelopeResponse);
    }
}
