# Sync Kafka

This source push data in kafka. You can use the bundle [koco/messenger-kafka](https://github.com/KonstantinCodes/messenger-kafka) to do it.

## koco/messenger-kafka install

```
composer require koco/messenger-kafka
```

## Configuration reference

```
framework:
    messenger:
        transports:
            producer:
                dsn: "%env(MESSENGER_TRANSPORT_DSN)%" // kafka://localhost:9092
                options:
                    topic:
                        name: "%env(MESSENGER_TRANSPORT_TOPIC)%" // topic_name
```
dsn | the url you want to collect (needs to start by ftp or sftp)
retry_strategy.max_retries | needs to be 0 because ftp transport does not support this feature
options.topic.name | Name of the topic what you consume
options.kafka_conf | to configure this, thanks to report to this [documentation](https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md#global-configuration-properties)
options.topic_conf | to configure this, thanks to report to this [documentation](https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md#topic-configuration-properties)

## Monitoring

Monitoring is automatically active on producer, it will track the following metrics :

* Counter produce
* Counter error and error code
* Memory usage
* Exection time
