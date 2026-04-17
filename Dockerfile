FROM php:8.2-cli

# Force DNS servers
RUN echo "nameserver 8.8.8.8" > /etc/resolv.conf && \
    echo "nameserver 1.1.1.1" >> /etc/resolv.conf

RUN docker-php-ext-install mysqli
COPY . /var/www/html/
EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]
