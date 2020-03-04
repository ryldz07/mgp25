# The php:7.0-apache Docker image is based on debian:jessie.
# See: https://github.com/docker-library/php/blob/20b89e64d16dc9310ba6493a38385e36304dded7/7.0/Dockerfile

FROM php:7.1-apache-jessie
RUN echo "deb http://deb.debian.org/debian jessie main" > /etc/apt/sources.list \
    && echo "deb http://security.debian.org jessie/updates main" >> /etc/apt/sources.list \
    && apt-get update \
    && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        git \
        libav-tools \
        unzip \
        wget \
        xz-utils \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) bcmath \
    && docker-php-ext-install -j$(nproc) exif

RUN wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz \
      && tar Jxvf ./ffmpeg-release-amd64-static.tar.xz \
      && cp ./ffmpeg*amd64-static/ffmpeg /usr/local/bin/

# Install Composer and make it available in the PATH
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Set the WORKDIR to /app so all following commands run in /app
WORKDIR /var/www/html

COPY . ./

# Install dependencies with Composer.
RUN composer install --prefer-source --no-interaction
