FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx supervisor && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli

COPY public/ /var/www/html/
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiamo solo il pool www
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

EXPOSE 8080

CMD ["supervisord", "-n"]
