FROM php:8.2-fpm

WORKDIR /var/www/html

COPY index.php .

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
