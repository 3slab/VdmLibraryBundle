<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Vdm\Bundle\LibraryBundle\Client\Elastic\Event\ElasticClientReceivedEvent;
use Vdm\Bundle\LibraryBundle\EventListener\MonitoringElasticClientSubscriber;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringElasticClientSubscriberTest extends TestCase
{
    /**
     * @var StatsStorageInterface $statsStorageInterface
     */
    private $statsStorageInterface;

    protected function getSubscriber(array $calls)
    {
        $this->statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $this->responseInterface = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $this->statsStorageInterface->expects($calls['sendElasticResponseStat'])->method('sendElasticResponseStat');

        return new MonitoringElasticClientSubscriber($this->statsStorageInterface, new NullLogger());
    }

    /**
     * @dataProvider dataProviderTestOnElasticClientReceivedEvent
     */
    public function testOnElasticClientReceivedEvent($methodCall)
    {
        $listener = $this->getSubscriber(['sendElasticResponseStat' => $methodCall]);

        $response = [
            '_index' => 1,
            'result' => 'created',
            '_shards' => [
                'successful' => true
            ],
        ];
        $event = new ElasticClientReceivedEvent($response);

        $listener->onElasticClientReceivedEvent($event);
    }

    public function dataProviderTestOnElasticClientReceivedEvent()
    {
        yield [
            $this->once()
        ];
    }
}
