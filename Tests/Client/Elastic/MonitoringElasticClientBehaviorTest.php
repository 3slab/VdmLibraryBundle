<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\Client\Elastic;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Client\Elastic\ElasticClient;
use Vdm\Bundle\LibraryBundle\Client\Elastic\MonitoringElasticClientBehavior;
use Vdm\Bundle\LibraryBundle\Model\Message;

class MonitoringElasticClientBehaviorTest extends TestCase
{    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var ElasticClient $elasticClient
     */
    private $elasticClient;

    /**
     * @var MonitoringElasticClientBehavior $monitoringElasticClient
     */
    private $monitoringElasticClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->client = $this->getMockBuilder(\Elasticsearch\Client::class)->disableOriginalConstructor()->setMethods(['index'])->getMock();
        $this->elasticClient = $this->getMockBuilder(ElasticClient::class)->disableOriginalConstructor()->setMethods(['post'])->getMock();
        $this->elasticClient->setClient($this->client);
        $this->eventDispatcher = $this->getMockBuilder(\Psr\EventDispatcher\EventDispatcherInterface::class)->getMock();

        $this->monitoringElasticClient = new MonitoringElasticClientBehavior($this->logger, $this->elasticClient, $this->eventDispatcher);
    }

    public function testPost()
    {
        $index = 'test';
        $body = 'test';

        $params = [
            'index' => $index,
            'body'  => (is_array($body)) ? $body : [ 'message' => $body ]
        ];
        $reponseClient = $this->client->index($params);

        $envelope = new Envelope(new Message($body));
        $reponsePost = $this->monitoringElasticClient->post($envelope, $index);

        $this->assertSame($reponseClient, $reponsePost);
    }

    public function testPostException()
    {
        $elasticClient = $this
                    ->getMockBuilder(ElasticClient::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['post'])
                    ->getMock();
        
        $elasticClient->method('post')->willThrowException(new \Exception());
        $this->monitoringElasticClientException = new MonitoringElasticClientBehavior($this->logger, $elasticClient, $this->eventDispatcher);
        $this->expectException(\Exception::class);

        $envelope = new Envelope(new Message('test'));
        $this->monitoringElasticClientException->post($envelope, 'test');
    }
}
