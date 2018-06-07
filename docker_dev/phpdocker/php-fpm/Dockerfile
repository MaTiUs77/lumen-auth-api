FROM php:7.1-fpm

WORKDIR "/application"

RUN apt-get update \
    && apt-get install -y \
	zlib1g-dev \
        git \
        zip \
        unzip \
        libmcrypt-dev \
    && docker-php-ext-install -j$(nproc) pcntl pdo pdo_mysql iconv mcrypt \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer
