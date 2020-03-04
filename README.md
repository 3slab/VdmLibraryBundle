# VdmLibraryBundle

## Introduction

This is the Symfony bundle part of VDM.library component used as the foundation of all VDM.collect, VDM.compute and VDM.store components.

It provides services to integrate your instances of VDM.collect, VDM.compute and VDM.store components with all other core component of the VDM platform (VDM.backbone, VDM.control and VDM.flow)

## Services

* VDM.collect HTTP pull
* VDM.compute standard
* VDM.store doctrine
* VDM.backbone consumer
* VDM.backbone producer

## Features

* Collect HTTP sources :
    * Retry on error
* Produce message to a broker with exit on error
* Consume message from a broker with exit on error
* Process Message between consumption and production
* Monitoring :
    * Track usage (number of messages consumed, produced)
    * Track state (started, stopped, errored)
    * Track process metrics (memory, processing time)
    * Track dependencies metrics
        * http : sources states, response codes, response times, response size
        * doctrine : total query times, errors
* Logging

These features are built on top of the [Symfony Framework](https://symfony.com/) and its [messenger component](https://symfony.com/doc/current/components/messenger.html)

## Documentation

* [Getting started](./Resources/docs/getting-started.md)
* Sources :
    * [HTTP pull](./source/http-pull.md)
    * [Kafka](./source/kafka.md)
* Sync :
    * [Doctrine](./sync/doctrine.md)
    * [Kafka](./sync/kafka.md)
* [Monitoring](./Resources/docs/monitoring.md)
* [Configuration Reference](./Resources/docs/configuration-reference.md)
* Examples :
    * [Setup a VDM.collect instance to pull meteo data](./Resources/docs/examples/vdm-collect.md)
    * [Setup a VDM.compute instance to process meteo data](./Resources/docs/examples/vdm-compute.md)
    * [Setup a VDM.store instance to store meteo data](./Resources/docs/examples/vdm-store.md)