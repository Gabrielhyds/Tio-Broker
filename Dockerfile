# Usa a imagem oficial do PHP 8.2 com Apache como base.
FROM php:8.2-apache

# --- 1. Instalação de Dependências do Sistema e Extensões PHP ---
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli zip gd

# --- 2. Instalação do Composer ---
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- 3. Configuração do Apache ---
RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# --- 4. Define o Diretório de Trabalho ---
WORKDIR /var/www/html

# --- 5. Otimização de Cache e Instalação de Dependências ---
COPY ./Tio-Broker/composer.json ./Tio-Broker/composer.lock ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# --- 6. Copia os Arquivos do Projeto ---
COPY ./Tio-Broker/ /var/www/html/

# --- 7. Ajuste de Permissões ---
RUN chown -R www-data:www-data /var/www/html

# --- 8. Instala o Supervisor ---
RUN apt-get update && apt-get install -y supervisor

# --- 9. Copia o arquivo de configuração do Supervisor ---
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# --- 10. Exposição das portas ---
EXPOSE 80
EXPOSE 8080

# --- 11. Inicia Apache + Ratchet via Supervisor ---
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
