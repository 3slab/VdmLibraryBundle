# Manual Transport

VdmLibraryBundle provides a manual transport which can be used along side a 
service implementing `Vdm\Bundle\LibraryBundle\Transport\Manual\VdmManualExecutorInterface` to create a custom 
transport implementation on the fly.

This is an example of a vdm manual transport configuration in `messenger.yaml`

```
transports:
    custom_transport:
        dsn: vdm+manual://custom_transport_executor_service
        retry_strategy:
            max_retries: 0
```

Then you need to declare the service `custom_transport_executor_service` with a tag `vdm.manual_executor` and a key 
the term referenced in the messenger dsn setting :

```
my_custom_executor_service:
    class: App\Executor\MyCustomExecutorService
    tags:
        - { name: 'vdm.manual_executor', key: 'custom_transport_executor_service' }
```

Then your service `App\Executor\MyCustomExecutorService` needs to implement the interface 
[VdmManualExecutorInterface](../../Transport/Manual/VdmManualExecutorInterface.php). This interface is the same as any
messenger transport [TransportInterface](https://github.com/symfony/messenger/blob/5.x/Transport/TransportInterface.php)