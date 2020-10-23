# VdmLibraryBundle

## Introduction

This is the Symfony bundle part of the VDM.library used as the foundation to build VDM.collect, VDM.compute and 
VDM.store components.

It provides services to integrate your instances of VDM.collect, VDM.compute and VDM.store components with all other 
core component of the VDM platform (VDM.backbone, VDM.control and VDM.flow)

## Services

* VDM.collect HTTP pull
* VDM.collect FTP/SFTP
* VDM.backbone consumer
* VDM.backbone producer
* VDM.store Elasticsearch
* VDM.store Doctrine

## Features

* Collect HTTP sources :
    * Retry on error
    * Monitoring of HTTP Responses
* Produce message to a broker with exit on error
* Consume message from a broker with exit on error
* Handle Message between consumption and production
* Monitoring :
    * Track usage (number of messages consumed, produced)
    * Track state (started, stopped, errored)
    * Track process metrics (memory, processing time)
    * Track dependencies metrics
        * http : sources states, response codes, response times, response size
        * ftp : source states, errors and file size

These features are built on top of the [Symfony Framework](https://symfony.com/) and 
its [messenger component](https://symfony.com/doc/current/components/messenger.html)

## Documentation

* [Concepts](./Resources/docs/concepts.md)
* [Getting started](./Resources/docs/getting-started.md)
* Consume :
    * [RabbitMQ json](./Resources/docs/consume/rabbitmq-json.md)
    * [HTTP pull](./Resources/docs/consume/http-pull.md)
    * [Kafka](./Resources/docs/consume/kafka.md)
    * [FTP/SFTP](./Resources/docs/consume/ftp.md)
* Produce :
    * [Kafka](./Resources/docs/produce/kafka.md)
    * [Elasticsearch](./Resources/docs/produce/elasticsearch.md)
    * [Doctrine ORM](./Resources/docs/produce/doctrine_orm.md)
    * [Doctrine ODM](./Resources/docs/produce/doctrine_odm.md)
* [Manual Tranport](./Resources/docs/manual_transport.md)    
* [Monitoring](./Resources/docs/monitoring.md)
* [Configuration Reference](./Resources/docs/configuration-reference.md)
* [Docker](./Resources/docs/docker.md)
* Examples :
    * [Setup a VDM.collect instance to pull meteo data](./Resources/docs/examples/vdm-collect.md)
    * [Setup a VDM.compute instance to process meteo data](./Resources/docs/examples/vdm-compute.md)
    * [Setup a VDM.store instance to store meteo data](./Resources/docs/examples/vdm-store.md)
    
## License

This bundle is distributed around the [MIT License](./LICENSE)
