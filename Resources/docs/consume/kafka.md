# Source Kafka

This source can collect data from a Kafka. To collect from kafka, I should you to use [koco/messenger-kafka](https://github.com/KonstantinCodes/messenger-kafka)

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

To configure this part, thanks to report on the [symfony documentation](https://symfony.com/doc/current/messenger.html).
