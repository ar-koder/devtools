FROM thecodingmachine/php:8.1-v4-apache-node16

COPY --chown=docker:docker . .
USER docker

ENV TEMPLATE_PHP_INI "production"
ENV PHP_INI_MEMORY_LIMIT="128M"

ENV PHP_EXTENSIONS="gd pdo_sqlite" \
    APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data \
    APACHE_DOCUMENT_ROOT=public/

RUN composer install --no-progress --no-interaction --optimize-autoloader

RUN php bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction
RUN php bin/console doctrine:fixtures:load --no-interaction

RUN composer dump-env prod

RUN npm ci
RUN npm run build

ENV APP_ENV "prod"
ENV APP_DEBUG "0"

