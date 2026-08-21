FROM php:8.2-fpm

# Install nginx + supervisor
RUN apt-get update && apt-get install -y nginx supervisor && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli

# Copy application files
COPY public/ /var/www/html/

# Copy nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy supervisord configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Railway expects the container to listen on port 8080
EXPOSE 8080

# Start both nginx and php-fpm via supervisord
CMD ["supervisord", "-n"]
