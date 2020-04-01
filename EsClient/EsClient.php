<?php

namespace Vdm\Bundle\LibraryBundle\EsClient;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;

class EsClient implements EsClientInterface
{
    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @var Client $client 
     */
    private $client;

    public function __construct(string $host, int $port, string $user, string $password, string $scheme, LoggerInterface $messengerLogger) {
        $this->logger = $messengerLogger;
        $hosts = [
            // This is effectively equal to: "https://username:password!#$?*abc@host:port"
            [
                'host' => $host,
                'port' => $port,
                'scheme' => $scheme,
                'user' => $user,
                'pass' => $password
            ],
        ];
        
        $this->client = ClientBuilder::create()           // Instantiate a new ClientBuilder
                                    ->setHosts($hosts)    // Set the hosts
                                    ->build();
    }

    public function post(Envelope $envelope, string $index): ?array
    {
        $body = $envelope->getMessage()->getPayload();

        $params = [
            'index' => $index,
            'body'  => [ 'message' => $body ]
        ];

        return $this->client->index($params);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
