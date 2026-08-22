FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx supervisor && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli

# Elimina eventuali file residui copiati per errore
RUN rm -f /var/www/html/supervisord.conf /var/www/html/supervisor.conf /var/www/html/nginx.conf

# Copia i file PHP
COPY public/ /var/www/html/

# Copia le configurazioni corrette
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["supervisord", "-n"]
