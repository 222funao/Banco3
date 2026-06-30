FROM php:8.3-apache-bookworm

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql opcache \
    && rm -rf /var/lib/apt/lists/* \
    && (a2dismod mpm_event mpm_worker || true) \
    && a2enmod mpm_prefork rewrite

WORKDIR /var/www/html
COPY . /var/www/html
COPY docker-entrypoint-railway.sh /usr/local/bin/docker-entrypoint-railway

RUN chmod +x /usr/local/bin/docker-entrypoint-railway \
    && chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD ["docker-entrypoint-railway"]
