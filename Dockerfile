FROM php:8.2.0-cli

RUN apt update && \
    apt install --no-install-recommends -y \
    git \
    vim \
    openssl \
    libcurl4-openssl-dev \
    libxslt-dev \
    zlib1g-dev \
    libicu-dev \
    libzip-dev \
    unzip
    #apt-get install libssl-dev -y && \

RUN docker-php-ext-install intl xsl

RUN curl -sS https://get.symfony.com/cli/installer | bash

COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /usr/src/app
COPY ./ /usr/src/app

RUN composer install --no-scripts --no-interaction
RUN chmod -R ug+x ./bin