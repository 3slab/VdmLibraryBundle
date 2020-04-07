# Source Ftp Pull

This source can collect data from a ftp server.

## Configuration reference

```
framework:
    messenger:
        transports:
            consumer:
                dsn: "sftp://user:password@sftp.fr:2222"
                retry_strategy:
                    max_retries: 0
                options:
                    monitoring:
                        enabled: true
                    mode: move
                    ftp_options:
                        dirpath: path/to/your/files/
                        storage: path/to/your/storage/
```

Configuration | Description
--- | ---
dsn | the url you want to collect (needs to start by ftp or sftp)
retry_strategy.max_retries | needs to be 0 because ftp transport does not support this feature
options.mode | two mode available (move|delete), `move` to deplace the file in other folder when it is treated, `delete` to remove it.
options.ftp_options | options to manage your ftp actions
options.ftp_options.dirpath | path to your directory
options.ftp_options.storage | If you choose option `move` you have to configure this path.
options.monitoring.enabled | if true, hook up in the vdm library bundle monitoring system to send information about the FTP response

## Custom ftp executor

A custom ftp executor allows you to customize how you call the ftp server. It's necessary if you have differents action to make on files.

Just create a class in your project that extends `Vdm\Bundle\LibraryBundle\Executor\Ftp\AbstractFtpExecutor`. It will
automatically replace the default executor.

**If you have 2 custom executor. Only a single one will be used, the second is ignored.**

```
namespace App\Ftp\FtpExecutor;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Executor\Ftp\AbstractFtpExecutor;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class CustomFtpExecutor implements AbstractFtpExecutor
{
    /** 
     * @var LoggerInterface 
    */
    private $logger;

    public function __construct(LoggerInterface $logger) 
    {
        parent::__construct();
        $this->logger = $logger;
    }

    public function execute(array $files): iterable
    {
        if (count($files) === 0) {
            yield new Envelope(new Message(""), [new StopAfterHandleStamp()]);
        }
        foreach ($files as $file) {
            if (isset($file['type']) && $file['type'] === 'file') {
                $file = $this->ftpClient->get($file);
                $message = new Message($file);
                // Put the stop stamp on the last file
                if (next($files) === true){
                    yield new Envelope($message);
                } else {
                    yield new Envelope($message, [new StopAfterHandleStamp()]);
                }
            } else {
                yield new Envelope(new Message(""), [new StopAfterHandleStamp()]);
            }
        }
    }
}
```

There are 2 important things your custom executor needs to do :

* `yield` a new envelope with a VDM Message instance
* Add a `StopAfterHandleStamp` stamp to the yielded envelope if you want to stop after handling the last file (if not, 
  the messenger worker loop over and will execute it once again).

*Note : thanks to the yield system, you can implement a loop in your execute function and return items once at a time*

*Note : you can keep state in your custom executor so if it is executed again, adapt your ftp call*

## Monitoring

If you enable monitoring, it will track the following metrics :

* Size of the Ftp file body
* Counter the ftp error