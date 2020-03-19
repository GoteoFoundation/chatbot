FROM php:7.4-fpm-alpine
LABEL maintainer="ivan@platoniq.net"

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN apk add --update --no-cache npm

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

COPY --chown=www-data:www-data docker/src/ ./

RUN composer install

RUN { \
        echo '#!/bin/sh'; \
        echo '/var/www/html/artisan --ansi "$@"'; \
    } > /usr/local/bin/artisan && chmod +x /usr/local/bin/artisan

RUN npm install
RUN npm run prod

EXPOSE 9000

COPY docker/wait-for /usr/local/bin/wait-for
COPY docker/prod/entrypoint.sh /usr/local/bin/chatbot-entrypoint

ENTRYPOINT ["chatbot-entrypoint"]
CMD ["php-fpm"]

# todo create entry point with database installation if none installed
