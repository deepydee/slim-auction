FROM php:8.3-fpm-alpine3.18

ENV XDEBUG_VERSION 3.3.1

RUN apk add --no-cache postgresql-dev fcgi git linux-headers \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && git clone --branch $XDEBUG_VERSION --depth 1 https://github.com/xdebug/xdebug.git /usr/src/php/ext/xdebug \
    && git clone --depth 1 https://github.com/wikimedia/mediawiki-php-excimer.git /usr/src/php/ext/excimer \
    && docker-php-ext-configure xdebug --enable-xdebug-dev \
    && docker-php-ext-install pdo_pgsql xdebug excimer \
    && docker-php-ext-enable excimer \
    && apk del git linux-headers

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

RUN addgroup -g 1000 webserver && adduser -G webserver -g webserver -s /bin/sh -D webserver

COPY --chown=webserver ./common/php/conf.d /usr/local/etc/php/conf.d
COPY --chown=webserver ./development/php/conf.d /usr/local/etc/php/conf.d
COPY --chown=webserver ./development/php-fpm/conf.d /usr/local/etc/php/conf.d
COPY --chown=webserver ./common/php/php-fpm.d /usr/local/etc/php-fpm.d

WORKDIR /app

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s \
    CMD REDIRECT_STATUS=true SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
    cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1