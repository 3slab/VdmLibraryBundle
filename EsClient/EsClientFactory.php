<?php

namespace Vdm\Bundle\LibraryBundle\EsClient;

use Psr\Log\LoggerInterface;

class EsClientFactory implements EsClientFactoryInterface
{
    /**
     * @var LoggerInterface $messengerLogger
     */
    private $logger;

    public function __construct(LoggerInterface $messengerLogger) {
        $this->logger = $messengerLogger;
    }

    public function create(string $dsn, ?array $options): EsClient
    {
        $dsn_regex = '/^((?P<driver>\w+):\/\/)?((?P<user>\w+)?(:(?P<password>\w+))?@)?(?P<host>[\w-\.]+)(:(?P<port>\d+))?$/Uim';
        
        $scheme =  (isset($options['scheme'])) ? $options['scheme'] : "https";
        if (false == preg_match($dsn_regex, $dsn, $result)) {
            throw new \InvalidArgumentException("DSN invalide"); 
        }

        return new EsClient($result['host'], $result['port'], $result['user'], $result['password'], $scheme, $this->logger);   
    }
}
