FROM php:8.2-apache

# Instala a extensão mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Desativa módulos MPM conflitantes e ativa o prefork + rewrite
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

# Altera a porta do Apache de 80 para 8080 no arquivo de configuração
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Copia os arquivos do projeto
COPY . /var/www/html/

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080
