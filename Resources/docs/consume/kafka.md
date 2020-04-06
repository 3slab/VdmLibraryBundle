# Source Kafka

This source can collect data from a Kafka. To collect from kafka, I should you to use [koco/messenger-kafka](https://github.com/KonstantinCodes/messenger-kafka)

## koco/messenger-kafka install

```
composer require koco/messenger-kafka
```

## Configuration reference

```
framework:
    messenger:
        transports:
            consumer:
                dsn: "%env(MESSENGER_TRANSPORT_DSN)%" // kafka://localhost:9092
                options:
                    topic:
                        name: "%env(MESSENGER_TRANSPORT_TOPIC_CONSUMER)%" // topic_name
                    kafka_conf:
                        enable.auto.offset.store: "false"
                        group.id: '%env(MESSENGER_TRANSPORT_TOPIC_GROUP)% // topic_groupe'
                    topic_conf:
                        auto.offset.reset: "earliest"
```

Configuration | Description
--- | ---
dsn | the url you want to collect (needs to start by ftp or sftp)
retry_strategy.max_retries | needs to be 0 because ftp transport does not support this feature
options.topic.name | Name of the topic what you consume
options.kafka_conf | to configure this, thanks to report to this [documentation](https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md#global-configuration-properties)
options.topic_conf | to configure this, thanks to report to this [documentation](https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md#topic-configuration-properties)

## Monitoring

Monitoring is automatically active on consumer, it will track the following metrics :

* Counter consume
* Counter error and error code
* Memory usage
* Exection time
