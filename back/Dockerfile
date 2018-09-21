FROM php:7.1-fpm-alpine
MAINTAINER Louis TREZZINI <louis.trezzini@ponts.org>

RUN addgroup php && adduser -D -G php php
RUN mkdir /app && chown php:php /app && chmod 700 /app
WORKDIR /app

RUN apk --no-cache add git libmcrypt-dev libjpeg-turbo-dev libpng-dev freetype-dev

RUN docker-php-ext-install pdo pdo_mysql iconv mcrypt && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd


COPY php-fpm.conf /etc/php-fpm/www.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer config --global repo.packagist composer https://packagist.org

# Set timezone
RUN ln -snf /usr/share/zoneinfo/Europe/Paris /etc/localtime && echo Europe/Paris > /etc/timezone && \
    printf '[PHP]\ndate.timezone = "Europe/Paris"\n',  > /usr/local/etc/php/conf.d/tzone.ini

USER php

ENV APP_ENV=prod
COPY composer.json composer.lock /app/
RUN echo $APP_ENV && composer install $( [[ "$APP_ENV" == "prod" ]] && echo "--no-ansi --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader")

COPY --chown=php:php . /app/

RUN create-circle-version.sh

ENTRYPOINT ["/app/entrypoint.sh"]
CMD ["php-fpm"]
EXPOSE 9000
