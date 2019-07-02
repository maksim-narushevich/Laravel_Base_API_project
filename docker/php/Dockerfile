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
            supervisor \
            && docker-php-ext-configure zip --with-libzip \
            && printf "\n" | pecl install redis \
            && docker-php-ext-install -j$(nproc) pdo_mysql zip mysqli bcmath sockets\
            && docker-php-ext-enable redis
#            && pecl install xdebug \
#            && docker-php-ext-enable xdebug

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install plugin for parallel packages installation
RUN php /usr/local/bin/composer global require hirak/prestissimo

# Set working directory PHP
WORKDIR /var/www

# copy files to image
COPY . /var/www

#######################
# Configure Supervisor
#######################
# Create Supervisor group
RUN groupadd supervisor
# Add user to Supervisor group
RUN usermod -a -G supervisor root
COPY ./docker/supervisor/supervisord.conf /etc/supervisor
COPY ./docker/supervisor/* /etc/supervisor/conf.d/
# Set Supervisor environment
RUN cd /var/www && mkdir -p logs/supevisor
RUN service supervisor restart
RUN touch /var/run/supervisor.sock
RUN chmod 777 /var/run/supervisor.sock
### In order to Start Supervisor run:
### ./services/start_supervisor.sh
#######################


# copy nginx config to image
COPY ./docker/nginx/hosts/. /etc/nginx/conf.d

#VOLUME /var/www

# Run PHP-FPM server
CMD ["php-fpm"]

