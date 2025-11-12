# Dockerfile
FROM php:8.2-apache

# Instala as dependências necessárias
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        libonig-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        zip \
        unzip \
    && rm -rf /var/lib/apt/lists/*

# Instala as extensões PHP (PostgreSQL, PDO, GD)
RUN docker-php-ext-configure gd --with-webp --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring gd exif

# Ativa o módulo de rewrite do Apache (necessário para .htaccess)
RUN a2enmod rewrite

# Copia todos os arquivos do seu projeto para o diretório raiz do Apache
COPY . /var/www/html/

# Define as permissões corretas para o Apache
RUN chown -R www-data:www-data /var/www/html/

# Expondo a porta 80 (padrão do Apache)
EXPOSE 80