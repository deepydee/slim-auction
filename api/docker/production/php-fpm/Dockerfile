FROM php:8.3-cli-alpine3.18 AS builder

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --version=2.8.4 --quiet

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --optimize-autoloader \
    && rm -rf /root/.composer/cache

### FPM ###

FROM php:8.3-cli-alpine3.18

RUN apk add --no-cache postgresql-dev fcgi git \
    && git clone --depth 1 https://github.com/wikimedia/mediawiki-php-excimer.git /usr/src/php/ext/excimer \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql opcache excimer \
    && docker-php-ext-enable excimer

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/production/php/conf.d /usr/local/etc/php/conf.d

COPY ./docker/common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

RUN addgroup -g 1000 webserver && adduser -G webserver -g webserver -s /bin/sh -D webserver

WORKDIR /app

COPY --chown=webserver --from=builder /app ./
COPY --chown=webserver ./ ./

USER webserver

