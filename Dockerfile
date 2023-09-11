# Utilisez l'image PHP avec Apache
FROM php:8.1-apache

# Installez les dépendances
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# Copiez les fichiers de l'application dans le conteneur
COPY . /var/www/html

# Configurez Apache pour servir l'application Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN a2enmod rewrite

# Exposez le port 80
EXPOSE 800

# Commande par défaut pour démarrer Apache
CMD ["apache2-foreground"]
