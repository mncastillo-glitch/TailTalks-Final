FROM php:8.2-apache

# Fix MPM conflict
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true && \
    a2enmod mpm_prefork

# Install mysqli
RUN docker-php-ext-install mysqli

COPY . /var/www/html/
