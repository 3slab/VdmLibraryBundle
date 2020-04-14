<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Transport\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Vdm\Bundle\LibraryBundle\Exception\Doctrine\UndefinedEntityException;
use Vdm\Bundle\LibraryBundle\Executor\Doctrine\AbstractDoctrineExecutor;
use Vdm\Bundle\LibraryBundle\Executor\Doctrine\DoctrineExecutorConfigurator;
use Vdm\Bundle\LibraryBundle\Transport\Doctrine\DoctrineSender;

class DoctrineSenderFactory
{
    /**
     * @var AbstractDoctrineExecutor
     */
    protected $entityManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        AbstractDoctrineExecutor $executor,
        LoggerInterface $logger = null
    ) {
        $this->executor = $executor;
        $this->logger   = $logger ?? new NullLogger();
    }

    /**
     * Created the DoctrineSender object based on messenger configuration.
     *
     * @param  array  $options
     *
     * @return DoctrineSender
     */
    public function createDoctrineSender(): DoctrineSender
    {
        $sender = new DoctrineSender($this->executor, $this->logger);

        return $sender;
    }
}
