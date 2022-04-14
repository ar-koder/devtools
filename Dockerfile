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

RUN npm cache clean --force
RUN composer clear-cache

RUN rm -rf node_modules

ENV APP_ENV "prod"
ENV APP_DEBUG "0"
ENV APP_SECRET "455159ee27d159bdbdeb8b34e599e80e"
ENV MESSENGER_TRANSPORT_DSN "doctrine://default?auto_setup=0"
ENV BUCKET_MODE "path"
ENV DATABASE_URL "sqlite:///%kernel.project_dir%/var/data.db"
ENV CORS_ALLOW_ORIGIN "^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$"
ENV TRUSTED_PROXIES "127.0.0.1,REMOTE_ADDR"
ENV IS_DOCKERIZED "1"


VOLUME ["var/bins", "var/log"]
