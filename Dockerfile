# Step 1: Use an official PHP image with Apache
FROM php:8.0-apache

# Step 2: Install required PHP extensions (for SQLite support)
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

# Step 3: Copy your PHP code into the container
COPY ./ /var/www/html/

# Step 4: Set proper permissions for SQLite database file
RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html

# Step 5: Expose port 80 to access the web server
EXPOSE 80

# Step 6: Start Apache server
CMD ["apache2-foreground"]
