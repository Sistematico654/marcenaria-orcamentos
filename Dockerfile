FROM php:8.2-cli

# Instala a extensão mysqli necessária para conectar ao MySQL
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copia todos os ficheiros do projeto para o container
WORKDIR /app
COPY . /app

# Expõe a porta 8080 configurada no Railway
EXPOSE 8080

# Inicia o servidor web embutido do PHP na porta 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app"]