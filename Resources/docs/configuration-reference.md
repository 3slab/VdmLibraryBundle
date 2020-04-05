# Configuration reference

```
vdm_library:
    app_name: default
    monitoring:
        type: null
        options: ~
```


Configuration | Default | Description
--- | --- | ---
app_name | `default` | Set a custom label on the monitoring metrics
monitoring.type | `null` | type of monitoring storage
monitoring.options | `~` | array passed to monitoring client constructor

Configuration for transport is in the transport documentation :

* Sources :
    * [HTTP pull](./consume/http-pull.md)
    * [Kafka](./consume/kafka.md)
* Destinations :
    * [Elasticsearch](./produce/elasticsearch.md)
    * [Kafka](./produce/kafka.md)
    