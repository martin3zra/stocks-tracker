FROM php:fpm

RUN apt-get update

RUN apt-get install -y --no-install-recommends \
    build-essential \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmagickwand-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
&& apt-get clean all

RUN docker-php-ext-install -j "$(nproc)" \
    pdo \
    pdo_mysql \
    mysqli \
    bcmath \
    exif \
    gd \
    intl \
    sockets \
    zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

RUN set -eux; \
    docker-php-ext-enable opcache; \
    { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_string_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN adduser stocks
USER stocks

CMD ["php-fpm"]

