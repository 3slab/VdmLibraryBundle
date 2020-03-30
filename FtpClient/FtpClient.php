<?php

namespace Vdm\Bundle\LibraryBundle\FtpClient;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Ftp as Adapter;
use League\Flysystem\Sftp\SftpAdapter;

class FtpClient implements FtpClientInterface
{
    private const DSN_PROTOCOL_FTP = 'ftp';
    private const DSN_PROTOCOL_SFTP = 'sftp';

    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    protected function getClient(array $result, array $options, bool $sftp = false): Filesystem
    {
        if ($this->filesystem === null) {
            if ($sftp) {
                $this->filesystem = new Filesystem(new SftpAdapter([
                    'host' => $result['host'],
                    'port' => (isset($result['port'])) ? $result['port'] : 22,
                    'username' => $result['user'],
                    'password' => $result['password'],
                    'privateKey' => (isset($options['privateKey'])) ? $options['privateKey'] : '',
                    'root' => (isset($options['root'])) ? $options['root'] : '',
                    'timeout' => (isset($options['timeout'])) ? $options['timeout'] : 10,
                ]));
            } else {
                $this->filesystem = new Filesystem(new Adapter([
                    'host' => $result['host'],
                    'username' => $result['user'],
                    'password' => $result['password'],
                
                    /** optional config settings */
                    'port' => (isset($result['port'])) ? $result['port'] : 21,
                    'root' => (isset($options['root'])) ? $options['root'] : '',
                    'passive' => (isset($options['passive'])) ? $options['passive'] : true,
                    'ssl' => (isset($options['ssl'])) ? $options['ssl'] : true,
                    'timeout' => (isset($options['timeout'])) ? $options['timeout'] : 30,
                    'ignorePassiveAddress' => (isset($options['ignorePassiveAddress'])) ? $options['ignorePassiveAddress'] : false,
                ]));
            }
        }

        return $this->filesystem;
    }

    public function get(string $dsn, array $options): ?array
    {
        $dsn_regex = '/^((?P<driver>\w+):\/\/)?((?P<user>\w+)?(:(?P<password>\w+))?@)?(?P<host>[\w-\.]+)(:(?P<port>\d+))?$/Uim';
        
        if (false == preg_match($dsn_regex, $dsn, $result)) {
            throw new \InvalidArgumentException("DSN invalide"); 
        }

        if (0 === strpos($result['driver'], self::DSN_PROTOCOL_FTP)) {
            $this->filesystem = $this->getClient($result, $options);
        } elseif (0 === strpos($result['driver'], self::DSN_PROTOCOL_SFTP)) {
            $this->filesystem = $this->getClient($result, $options, true);
        } else {
            throw new \InvalidArgumentException("Driver invalide"); 
        }

        $fichier = null;
        if ($this->filesystem->has($options['dirpath'])) {
            $files = $this->filesystem->listContents($options['dirpath']);

            if (isset($files[0]) && $files[0]['type'] === 'file') {
                $files[0]['content'] = $this->filesystem->read($options['dirpath'].'/'.$files[0]['basename']);
                $fichier = $files[0];
            }
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
