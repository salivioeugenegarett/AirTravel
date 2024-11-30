FROM php:8.0-apache

RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

COPY ./ /var/www/html/

RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
