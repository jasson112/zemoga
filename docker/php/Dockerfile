FROM php:7.1.30-fpm

COPY conf /usr/local/etc/php

RUN chmod 1777 /tmp

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libxrender1 \
        libfontconfig1 \
        libxext6 \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) mysqli \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
	&& docker-php-ext-install -j$(nproc) zip
	
RUN curl -sL https://deb.nodesource.com/setup_8.x | \
    apt-get install -y nodejs

WORKDIR /usr/share/nginx/html/web