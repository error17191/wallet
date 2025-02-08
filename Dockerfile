FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip unzip curl

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN chown -R www-data:www-data /var/www  \
    && chmod -R 755 /var/www \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
