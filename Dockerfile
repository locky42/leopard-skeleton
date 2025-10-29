FROM php:8.3-apache

# Install required PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libsqlite3-dev \
    libpq-dev \
    sqlite3 \
    zip \
    unzip \
    git \
    postgresql \
    postgresql-contrib \
    && docker-php-ext-install dom

# Install PDO SQLite and PDO PostgreSQL extensions
RUN docker-php-ext-install pdo_sqlite pdo_pgsql

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN rm -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Pass build arguments
ARG XDEBUG_MODE
ARG XDEBUG_START_WITH_REQUEST
ARG XDEBUG_CLIENT_HOST
ARG XDEBUG_CLIENT_PORT
ARG XDEBUG_LOG_LEVEL

# Configure Xdebug
RUN echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.mode=${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.start_with_request=${XDEBUG_START_WITH_REQUEST}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_port=${XDEBUG_CLIENT_PORT}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.log=/var/www/html/storage/logs/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.output_dir=/var/www/html/storage/logs/xdebug" >> /usr/local/etc/php/conf.d/xdebug.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Configure Apache to use the public folder as the root
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|' /etc/apache2/apache2.conf && \
    a2enmod rewrite

# Allow .htaccess overrides
RUN sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/apache2.conf

# Install PHP dependencies via Composer
RUN composer install --no-dev --optimize-autoloader

# Add ServerName to Apache config to suppress warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install pgvector extension
RUN apt-get install -y postgresql-server-dev-all && \
    git clone https://github.com/pgvector/pgvector.git && \
    cd pgvector && \
    make && \
    make install && \
    cd .. && rm -rf pgvector

# Clean up unnecessary files to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Setting sqlite database file creation and permissions
RUN [ -f /var/www/html/storage/database/db.sqlite ] || touch /var/www/html/storage/database/db.sqlite
RUN chown -R www-data:www-data storage/database
RUN chmod 776 /var/www/html/storage/database/db.sqlite
