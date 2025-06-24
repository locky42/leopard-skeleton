FROM php:8.4-apache

# Встановлюємо Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копіюємо код у контейнер
COPY . /var/www/html

# Встановлюємо права власності
RUN chown -R www-data:www-data /var/www/html

# Налаштовуємо Apache для використання папки public як кореневої
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|' /etc/apache2/apache2.conf && \
    a2enmod rewrite

# Дозволяємо переопределение конфігурації .htaccess
RUN sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/apache2.conf

# Встановлюємо залежності через Composer
RUN composer install --no-dev --optimize-autoloader

# Додаємо ServerName для усунення попередження
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
