FROM php:8.2-apache

# Activer mod_rewrite (routes propres via .htaccess)
RUN a2enmod rewrite

# Dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    pkg-config \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP SQL
RUN docker-php-ext-install pdo pdo_mysql

# Extension PHP MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Copier composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Pointer Apache sur /public (front controller)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Autoriser .htaccess
RUN printf '%s\n' \
  '<Directory /var/www/html/public>' \
  '    AllowOverride All' \
  '    Require all granted' \
  '</Directory>' \
  > /etc/apache2/conf-available/ecoride.conf && a2enconf ecoride

WORKDIR /var/www/html

# Évite le warning "dubious ownership" si git est utilisé dans le conteneur
RUN git config --global --add safe.directory /var/www/html

# Copier le code source
COPY . .
