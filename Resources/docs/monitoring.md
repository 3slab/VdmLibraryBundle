# Monitoring

The VDM library bundle monitoring system is plugged on the events dispatched by the messenger component in its
[Worker loop](https://github.com/symfony/messenger/blob/master/Worker.php).

## Tracked metrics

It tracks the following information :

* State of the worker : running/stopped (1/0)
* Exit code of the worker process : to detect if it has stopped because of an error (state != 0)
* Counter on the number of message received/consumed
* Counter on the number of message sent/produced
* Counter on the number of error during message handling
* Memory usage during message handling
* Time taken to handle a message

## Configuration

```
vdm_library:
    monitoring:
        type: null
        options: {}
```

## Storage

### Null storage

This is the default storage for monitoring events. It just logs that a monitoring events has occured.

### StatsD storage

It sends the monitoring events to a StatsD server.

The `monitoring.options` is passed to [DogStatsd constructor](https://github.com/DataDog/php-datadogstatsd/blob/master/src/DogStatsd.php#L70).


```
vdm_library:
    monitoring:
        type: statsd
        options:
            host: localhost
            port: 9125
            batched: false
            global_tags:
                key1: value1
```

If batched is true, it uses [BatchedDogStatsd class](https://github.com/DataDog/php-datadogstatsd/blob/master/src/BatchedDogStatsd.php) instead
of DogStatsd.