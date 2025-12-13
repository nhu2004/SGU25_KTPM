FROM php:8.2-apache

# Bật mod_rewrite (cho .htaccess)
RUN a2enmod rewrite

# Cài lib phục vụ cho GD + cài mysqli & gd
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli \
    && docker-php-ext-enable mysqli \
    && rm -rf /var/lib/apt/lists/*

# (tuỳ chọn) set timezone
ENV TZ=Asia/Ho_Chi_Minh

WORKDIR /var/www/html
