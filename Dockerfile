# Step 1: Use an official PHP image with Apache
FROM php:8.0-apache

# Step 2: Install required PHP extensions (for MySQL support)
RUN docker-php-ext-install mysqli

# Step 3: Copy your PHP code into the container
COPY ./ /var/www/html/

# Step 4: Configure Apache (Optional - If you want to change the DocumentRoot)
# COPY ./my-site.conf /etc/apache2/sites-available/000-default.conf

# Step 5: Expose port 80 to access the web server
EXPOSE 80

# Step 6: Start Apache server
CMD ["apache2-foreground"]
