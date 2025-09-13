# Dockerfile para PHP + Apache
FROM php:7.4-apache

# Instala extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Copia os arquivos da aplicação para o container
COPY . /var/www/html/

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html

# Define o diretório de trabalho
WORKDIR /var/www/html
