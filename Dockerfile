    # Gunakan Image PHP 8 dengan Apache
    FROM php:8-apache

    # Aktifkan mod_rewrite di Apache
    RUN a2enmod rewrite

    # Setel direktori kerja di dalam kontainer
    WORKDIR /app

    # Salin file-file composer
    COPY ./composer.json .
    COPY ./composer.lock .

    # Install dependensi PHP
    RUN apt-get update && \
        apt-get install -y git unzip libpq-dev && \
        docker-php-ext-install pdo pdo_pgsql && \
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
        composer install --no-scripts --no-autoloader && \
        rm -rf /var/lib/apt/lists/*

    # Salin sisa kode aplikasi
    COPY . .

    # Install dependensi PHP dengan autoloader
    RUN composer dump-autoload

    # Kenalkan port yang akan digunakan oleh aplikasi
    EXPOSE 8000