FROM php:7.4.33-cli-alpine3.16

ARG PHP_PATCH_BRANCH=PHP-7.4-CVE-2026-109618-109619

RUN set -eux; \
    apk add --no-cache --virtual .php-rebuild-deps \
        git \
        $PHPIZE_DEPS \
        linux-headers \
        bison \
        argon2-dev \
        curl-dev \
        gnu-libiconv-dev \
        libsodium-dev \
        libxml2-dev \
        oniguruma-dev \
        openssl-dev \
        readline-dev \
        sqlite-dev \
        zlib-dev; \
    rm -vf /usr/include/iconv.h; \
    ln -sfv /usr/include/gnu-libiconv/*.h /usr/include/; \
    docker-php-source extract; \
    rm -rf /usr/src/php; \
    git clone --depth=1 --branch "$PHP_PATCH_BRANCH" \
        https://github.com/php/php-src.git /usr/src/php; \
    cd /usr/src/php; \
    export CFLAGS="$PHP_CFLAGS" \
           CPPFLAGS="$PHP_CPPFLAGS" \
           LDFLAGS="$PHP_LDFLAGS"; \
    ./buildconf --force; \
    ./configure \
        --build=x86_64-linux-musl \
        --with-config-file-path="$PHP_INI_DIR" \
        --with-config-file-scan-dir="$PHP_INI_DIR/conf.d" \
        --enable-option-checking=fatal \
        --with-mhash \
        --with-pic \
        --enable-ftp \
        --enable-mbstring \
        --enable-mysqlnd \
        --with-password-argon2 \
        --with-sodium=shared \
        --with-pdo-sqlite=/usr \
        --with-sqlite3=/usr \
        --with-curl \
        --with-iconv=/usr \
        --with-openssl \
        --with-readline \
        --with-zlib \
        --enable-phpdbg \
        --enable-phpdbg-readline \
        --with-pear; \
    make -j"$(getconf _NPROCESSORS_ONLN)"; \
    make install; \
    docker-php-source delete; \
    apk del .php-rebuild-deps; \
    php -v

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
  && pecl install apcu amqp-1.11.0 mongodb-1.12.0 \
  && docker-php-ext-enable apcu \
  && docker-php-ext-enable amqp \
  && docker-php-ext-enable mongodb \
  && docker-php-ext-install gd intl xsl zip opcache sockets

#Download rdkafka
ENV LIBRDKAFKA_VERSION 1.4.0
ENV EXT_RDKAFKA_VERSION 4.0.3

RUN git clone --depth 1 --branch v$LIBRDKAFKA_VERSION https://github.com/edenhill/librdkafka.git \
  && cd librdkafka \
  && ./configure \
  && make \
  && make install

#Install rdkafka
RUN pecl install rdkafka-$EXT_RDKAFKA_VERSION \
  && docker-php-ext-enable rdkafka \
  && rm -rf /librdkafka

RUN wget https://getcomposer.org/installer -O composer-setup.php \
  && php ./composer-setup.php --version=2.6.6 --install-dir=/usr/local/bin \
  && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

CMD php bin/console messenger:consume consumer -vv
