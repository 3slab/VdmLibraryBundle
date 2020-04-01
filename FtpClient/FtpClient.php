<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Ftp as Adapter;
use League\Flysystem\Sftp\SftpAdapter;
use Psr\Log\LoggerInterface;

class FtpClient implements FtpClientInterface
{
    /**
     * @var LoggerInterface $messengerLogger
     */
    private $logger;

    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    public function __construct(
        string $host, 
        int $port, 
        string $user, 
        string $password, 
        bool $sftp, 
        array $options, 
        LoggerInterface $messengerLogger
    ) 
    {
        $this->logger = $messengerLogger;
        if ($sftp) {
            $this->filesystem = $this->filesystem = new Filesystem(new SftpAdapter([
                'host' => $host,
                'port' => $port,
                'username' => $user,
                'password' => $password,
                'privateKey' => (isset($options['privateKey'])) ? $options['privateKey'] : '',
                'root' => (isset($options['root'])) ? $options['root'] : '',
                'timeout' => (isset($options['timeout'])) ? $options['timeout'] : 10,
            ]));
        } else {
            $this->filesystem = new Filesystem(new Adapter([
                'host' => $host,
                'username' => $user,
                'password' => $password,
            
                /** optional config settings */
                'port' => $port,
                'root' => (isset($options['root'])) ? $options['root'] : '',
                'passive' => (isset($options['passive'])) ? $options['passive'] : true,
                'ssl' => (isset($options['ssl'])) ? $options['ssl'] : true,
                'timeout' => (isset($options['timeout'])) ? $options['timeout'] : 30,
                'ignorePassiveAddress' => (isset($options['ignorePassiveAddress'])) ? $options['ignorePassiveAddress'] : false,
            ]));
        }
        
    }

    public function get(string $dirpath): ?array
    {
        $fichier = null;
        if ($this->filesystem->has($dirpath)) {
            $files = $this->filesystem->listContents($dirpath);

            if (isset($files[0]) && $files[0]['type'] === 'file') {
                $files[0]['content'] = $this->filesystem->read($dirpath.'/'.$files[0]['basename']);
                $fichier = $files[0];
            }
        } else {
            $this->logger->info(sprintf('Directory %s inexistant sur le serveur', $dirpath));
        }
        
        return $fichier;
    }

    /**
     * @return Filesystem
     */
    public function getFileSystem(): Filesystem
    {
        return $this->filesystem;
    }
}
