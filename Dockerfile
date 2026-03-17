FROM php:8.3-apache

# Xdebug argument configuration
ARG XDEBUG_MODE=off
ARG XDEBUG_START_WITH_REQUEST=no
ARG XDEBUG_CLIENT_HOST=host.docker.internal
ARG XDEBUG_CLIENT_PORT=9003
ARG XDEBUG_LOG_LEVEL=0

# Download the automated PHP extension installer script
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system minimums, SSL certificate utility, and clean up as much junk as possible
RUN apt-get update && apt-get install -y --no-install-recommends sqlite3 zip unzip git ssl-cert \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /usr/share/man/*

# Install PHP extensions and force removal of all development build temporary files
RUN IPE_DONT_CLEAN=0 install-php-extensions dom pdo_sqlite pdo_pgsql pdo_mysql xdebug

# Xdebug configuration (using clean Heredoc style)
# Logs are routed to the mounted storage directory
RUN { \
    echo "xdebug.mode=${XDEBUG_MODE}"; \
    echo "xdebug.start_with_request=${XDEBUG_START_WITH_REQUEST}"; \
    echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}"; \
    echo "xdebug.client_port=${XDEBUG_CLIENT_PORT}"; \
    echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}"; \
    echo "xdebug.log=/var/www/html/storage/logs/xdebug/xdebug.log"; \
} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Composer installation
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Forcefully copy Apache configurations
COPY ./Docker/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./Docker/apache2/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

# Enable Apache modules and site configuration
RUN a2enmod rewrite ssl remoteip expires headers && a2ensite default-ssl.conf

# Ensure default Apache log directories exist as real folders, not symlinks
RUN rm -f /var/log/apache2/access.log /var/log/apache2/error.log \
    && mkdir -p /var/log/apache2 \
    && mkdir -p /var/www/html/storage/logs/apache2 \
    && mkdir -p /var/www/html/storage/logs/xdebug

# HARDCODE DOCUMENT ROOT INTO THE DEFAULT IMAGE VARIABLES
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

# Create directory for certificates
RUN mkdir -p /etc/apache2/ssl \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# DYNAMIC CHECK UPON CONTAINER START
CMD ["sh", "-c", "if [ ! -f /etc/apache2/ssl/project.pem ]; then \
        openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/apache2/ssl/project.key \
        -out /etc/apache2/ssl/project.pem \
        -subj '/C=US/ST=State/L=City/O=Organization/CN=localhost'; \
    fi && apache2-foreground"]
