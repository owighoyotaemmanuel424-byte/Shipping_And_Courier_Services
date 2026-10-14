FROM dunglas/frankenphp:php8.4-alpine

# Install the MySQL extensions required by the application's mysqli database layer.
RUN install-php-extensions mysqli pdo_mysql

WORKDIR /app
COPY . /app/

# FrankenPHP reads its application Caddyfile from this directory.
COPY Caddyfile /etc/frankenphp/Caddyfile

EXPOSE 8080
