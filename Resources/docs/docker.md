# Docker

A base docker file is provided with these PHP extensions for broker integration :

* [AMQP library](https://pecl.php.net/package/amqp)
* [RDKAFKA library](https://pecl.php.net/package/rdkafka)

You can extend this base image and COPY your source code into it and build your own by creating your own Dockerfile :

```
FROM 3slab/vdm-library-base:latest

COPY . /var/www/html
```

Then just build it :

```
docker build -t my-image:lastest .
```