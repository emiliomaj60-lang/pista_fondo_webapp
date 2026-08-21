FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli

COPY public/ /var/www/html/
COPY nginx.conf /etc/nginx/nginx.conf

# Avvia sia php-fpm che nginx
CMD sh -c "php-fpm -F & nginx -g 'daemon off;'"
