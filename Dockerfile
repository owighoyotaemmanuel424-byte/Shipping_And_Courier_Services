FROM php:8.4-apache

# Install the MySQL extensions required by the application's mysqli database layer.
RUN docker-php-ext-install mysqli pdo_mysql \
    && a2enmod rewrite

# Keep the repository root as the web document root.
WORKDIR /var/www/html
COPY . /var/www/html/

# Ensure Apache can read the application files.
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
