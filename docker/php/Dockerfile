FROM php:7.2.4-fpm

ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install --assume-yes apt-utils
RUN apt-get install -y \
            curl \
            wget \
            git \
            libzip-dev \
            expect \
            nano \
            zip \
            && docker-php-ext-configure zip --with-libzip \
            && printf "\n" | pecl install redis \
            && docker-php-ext-install -j$(nproc) pdo_mysql zip mysqli \
            && docker-php-ext-enable redis
#            && pecl install xdebug \
#            && docker-php-ext-enable xdebug

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install plugin for parallel packages installation
RUN php /usr/local/bin/composer global require hirak/prestissimo

# ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

# Set working directory PHP
WORKDIR /var/www

# copy files to image
COPY . /var/www

# copy nginx config to image
COPY ./docker/nginx/hosts/. /etc/nginx/conf.d

#VOLUME /var/www

# Run PHP-FPM server
CMD ["php-fpm"]
