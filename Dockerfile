FROM php:8-cli-alpine

RUN apk add --no-cache --virtual .build-deps \
  g++ git make autoconf yaml-dev libpng-dev cyrus-sasl-dev \
  zlib-dev

RUN pecl channel-update pecl.php.net

RUN apk update && apk upgrade \
  && apk add --no-cache \
        bash \
        openssh-client \
        wget \
        curl \
        libcurl \
        libzip-dev \
        bzip2-dev \
        openssl-dev \
        git \
        ca-certificates \
        linux-headers \
        libmcrypt-dev \
        libpng-dev \
        icu-dev \
        libxslt-dev \
        rabbitmq-c-dev \
        postgresql-dev \
  && pecl install apcu amqp-1.11.0beta mongodb \
  && docker-php-ext-enable apcu\
  && docker-php-ext-enable amqp\
  && docker-php-ext-enable mongodb\
  && docker-php-ext-install gd intl xsl zip opcache sockets pdo pdo_mysql pdo_pgsql

#Download rdkafka
ENV LIBRDKAFKA_VERSION 1.6.1
ENV EXT_RDKAFKA_VERSION 5.0.0

RUN git clone --depth 1 --branch v$LIBRDKAFKA_VERSION https://github.com/edenhill/librdkafka.git \
  && cd librdkafka \
  && ./configure \
  && make \
  && make install

#Install rdkafka
RUN pecl channel-update pecl.php.net \
  && pecl install rdkafka-$EXT_RDKAFKA_VERSION \
  && docker-php-ext-enable rdkafka \
  && rm -rf /librdkafka

RUN wget https://getcomposer.org/installer -O composer-setup.php \
  && php ./composer-setup.php  --install-dir=/usr/local/bin \
  && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

CMD php bin/console messenger:consume consumer -vv
