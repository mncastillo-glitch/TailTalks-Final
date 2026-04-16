FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    && a2dismod mpm_event 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

RUN docker-php-ext-install mysqli

COPY . /var/www/html/

EXPOSE 80
CMD ["apache2-foreground"]
