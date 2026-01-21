# Dockerfile para Laravel no AWS App Runner
FROM public.ecr.aws/lambda/provided:al2 as build

# Instala dependências do sistema
RUN yum install -y php php-mbstring php-xml php-pdo php-mysqlnd php-json php-zip php-gd php-curl php-bcmath php-tokenizer php-ctype php-fileinfo php-opcache php-dom php-pcntl php-posix php-intl php-cli unzip git && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/app/current

# Copia arquivos do projeto
COPY . .

# Instala dependências do PHP
RUN composer install --no-dev --optimize-autoloader

# Gera caches do Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Permissões
RUN chown -R nobody:nobody /var/app/current && chmod -R 755 /var/app/current

# Porta padrão do Laravel
EXPOSE 8080

# Comando de inicialização para App Runner
CMD ["/usr/bin/php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
