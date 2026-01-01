FROM php:8.2-apache

# Activer mod_rewrite (routes propres via .htaccess)
RUN a2enmod rewrite

# Installer extensions PHP nécessaires (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Pointer Apache sur /public (front controller)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
