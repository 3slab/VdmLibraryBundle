# Sync Elasticsearch

In case of you want to push data in your elsaticsearch cluster, you can use this producer.

## Configuration reference

```
framework:
    messenger:
        transports:
            producer:
                dsn: elasticsearch://localhost:9200
                options:
                    retry: 
                        enabled: false
                        number: 5
                        timeBeforeRetry: 5
                    monitoring:
                        enabled: true
                    es_conf:
                        index: test
                        scheme: http
```

Configuration | Description
--- | ---
dsn | the url you want to collect (needs to start by http or https)
options.es_conf | options to configure your Elastic Client
options.es_conf.index | index in which your push your data
options.es_conf.scheme | connection to your elastic search
options.monitoring.enabled | if true, hook up in the vdm library bundle monitoring system to send information about the HTTP response
options.retry.enabled | if true, retry an http call in case of error
options.retry.number | number of time to retry before stopping with error
options.retry.timeBeforeRetry | time in second between each try (multiplied by the current retry number to delay)

## Monitoring

If you enable monitoring, it will track the following metrics :

* Counter type of response (created/updated/...) by index