# Sync Kafka

This source push data in kafka. You can use the bundle [koco/messenger-kafka](https://github.com/KonstantinCodes/messenger-kafka) to do it.

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

To configure this part, thanks to report on the [symfony documentation](https://symfony.com/doc/current/messenger.html).
