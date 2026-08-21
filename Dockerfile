FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx && rm -rf /var/lib/apt/lists/*

COPY public/ /var/www/html/
COPY nginx.conf /etc/nginx/nginx.conf

CMD php-fpm -D && nginx -g "daemon off;"
