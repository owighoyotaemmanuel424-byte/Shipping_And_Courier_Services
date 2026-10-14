FROM dunglas/frankenphp:php8.4-alpine

# Install the MySQL extensions required by the application's mysqli database layer.
RUN install-php-extensions mysqli pdo_mysql

WORKDIR /app
COPY . /app/

# Serve the repository root because index.php and admin-setup.php live there.
COPY Caddyfile /etc/caddy/Caddyfile

EXPOSE 8080
