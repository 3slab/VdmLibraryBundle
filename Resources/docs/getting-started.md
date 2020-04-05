# Getting started

There are only 2 steps needed to have a working VDM component :

* [Install the bundle](#install-the-bundle)
* [Configure the bundle](#configure-the-bundle)

Then you can configure your [custom message handler](#custom-message-handler) and if your consumer supports it and 
you need it, a [custom executor](#custom-executor).

And finally [launch your process](#launch-your-process)

## Install the bundle

```
composer require 3slab/vdm-library-bundle
```

## Configure the bundle

You need to setup a consumer and producer. You can follow the 
[symfony messenger documentation](https://symfony.com/doc/current/messenger.html).

This is a simple example for a VDM.compute component with a consumption from kafka and a production to kafka using the 
kafka transport implementation from [koco/messenger-kafka](https://github.com/KonstantinCodes/messenger-kafka) :

```
framework:
    messenger:
        transports:
            consumer:
                dsn: "%env(KAFKA_URL)%"
                retry_strategy:
                    max_retries: 0
                options:
                    topic:
                        name: '%env(KAFKA_CONSUMER_TOPIC)%'
                    kafka_conf:
                        group.id: "my-consumer-id"
                        enable.auto.offset.store: "false"
                        bootstrap.servers: '%env(KAFKA_BOOTSTRAP_SERVERS)%'
                    topic_conf:
                        auto.offset.reset: "earliest"
            producer:
                dsn: "%env(KAFKA_URL)%"
                options:
                    topic:
                        name: '%env(KAFKA_PRODUCER_TOPIC)%'
                    kafka_conf:
                        bootstrap.servers: '%env(KAFKA_BOOTSTRAP_SERVERS)%'
```

## Custom message handler

VdmLibraryBundle provides a default message handler that is a simple pass-through that dispatches the message to the 
the message bus without acting on it in order for it to be produce by the producer. You can override this default 
handler by creating in your App namespace an handler that implements the 
[MessageSubscriberInterface](https://github.com/symfony/messenger/blob/master/Handler/MessageSubscriberInterface.php) or
the [MessageHandlerInterface](https://github.com/symfony/messenger/blob/master/Handler/MessageHandlerInterface.php). It
will automatically replace the default one.

**Only one handler replaces the default one**

The important part is to get the default message bus and to dispatch a VDM message into it at the end of your 
processing :

```
<?php

namespace App\MessageHandler;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CustomHandler implements MessageHandlerInterface
{
    protected $messengerLogger;

    protected $bus;

    public function __construct(LoggerInterface $messengerLogger, MessageBusInterface $bus)
    {
        $this->messengerLogger = $messengerLogger;
        $this->bus = $bus;
    }

    public function __invoke(Message $message)
    {
        $this->messengerLogger->debug("Execution of custom handler");

        $this->bus->dispatch($message);
    }
}

```

## Custom executor

Some transports provided by this bundle allows to customize the executor. The executor is an handler positionned just
after the get operation of the transport and before dispatching the message to the bus. It allows to implement things 
like filtering source, pre-handling of messages, ...

Look into the dedicated doc of the transport you want to use to see if it supports a custom executor. If it does, it
is like the message handler. Just define one that extends the abstract executor class or the executor interface 
for your transport and it will replace the default one.

**Only one executor replaces the default one**

## Launch your process

Run the following command :

```
php bin/console messenger:consume consumer
```

Or with `-vv` option to see details about what is happening
