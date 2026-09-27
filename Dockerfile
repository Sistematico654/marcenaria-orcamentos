FROM php:8.2-apache

# Instala a extensão mysqli para o MySQL
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Remove fisicamente qualquer configuração de MPM para evitar duplicados e ativa o prefork + rewrite
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/ \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/ \
    && a2enmod rewrite

# Ajusta o Apache para escutar na porta 8080 exigida pelo Railway
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Copia os ficheiros da aplicação
COPY . /var/www/html/

# Configura as permissões de acesso
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD ["apache2-foreground"]