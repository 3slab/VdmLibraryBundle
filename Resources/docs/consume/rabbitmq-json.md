# Source RabbitMQ json

This source can collect data from a RabbitMQ queue containing raw json (i.e. not a serialized enveloper like Messenger usuallly handles).

## Configuration reference

```
framework:
    messenger:
        transports:
            consumer:
                dsn: "vdm+amqp://guest:guest@rabbitmq:5672/%2f/request"
```

Configuration | Description
--- | ---
dsn | the queue you want to collect from (and its credentials)

## StdClassHandler

You will need to define an StdClassHandler in your application to handle messages pulled from the queue:

```php
class StdClassHandler implements MessageSubscriberInterface
{
    /** 
     * @var LoggerInterface $messengerLogger
    */
    protected $messengerLogger;

    public function __construct(LoggerInterface $messengerLogger)
    {
        $this->messengerLogger = $messengerLogger;
    }

    /**
     * Default handler implementation.
     * Does nothing on message because it is override by project code.
     *
     * @param Message $message
     */
    public function __invoke(stdClass $message)
    {
        $this->messengerLogger->debug("Execution of null handler");
        
        // Do something with the message
    }

    /**
     * {@inheritDoc}
     */
    public static function getHandledMessages(): iterable
    {
        // Low priority to be sure it is loaded after project handler and so removed from DI
        yield stdClass::class => [
            'priority' => -1000
        ];
    }
}
```