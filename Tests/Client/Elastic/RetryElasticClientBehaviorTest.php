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
use Vdm\Bundle\LibraryBundle\Client\Elastic\RetryElasticClientBehavior;
use Vdm\Bundle\LibraryBundle\Model\Message;

class RetryElasticClientBehaviorTest extends TestCase
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
     * @var RetryElasticClientBehavior $retryElasticClient
     */
    private $retryElasticClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
        $this->client = $this->getMockBuilder(\Elasticsearch\Client::class)->disableOriginalConstructor()->setMethods(['index'])->getMock();
        $this->elasticClient = $this->getMockBuilder(ElasticClient::class)->disableOriginalConstructor()->setMethods(['post'])->getMock();
        $this->elasticClient->setClient($this->client);
        $this->elasticClient->method('post')->willReturn(['result' => 'created']);
        $this->client->method('index')->willReturn(['result' => 'created']);
        $this->retryElasticClient = new RetryElasticClientBehavior($this->logger, $this->elasticClient, 5, 1);
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
        $reponsePost = $this->retryElasticClient->post($envelope, $index);

        $this->assertSame($reponseClient, $reponsePost);
    }

    public function testPostException()
    {
        $elasticClient = $this
                    ->getMockBuilder(ElasticClient::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['post'])
                    ->getMock();

        $retry = rand(1,4);
        
        $elasticClient->expects($this->exactly($retry))->method('post')->willThrowException(new \Exception());
        $retryElasticClientException = new RetryElasticClientBehavior($this->logger, $elasticClient, $retry-1, 1);
        $this->expectException(\Exception::class);

        $envelope = new Envelope(new Message('test'));
        $retryElasticClientException->post($envelope, 'test');
    }
}
