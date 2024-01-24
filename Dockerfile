FROM php:8.1-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install dev dependencies
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    # Install dependencies
    && apk add --no-cache \
#        redis \
    # Setup xdebug
    && install-php-extensions xdebug \
    && echo "xdebug.mode = debug,coverage" >> /usr/local/etc/php/conf.d/xdebug.ini \
    # Cleanup dev dependencies
    && apk del -f .build-deps
#    && sed -i -e 's/bind 127.0.0.1/bind 0.0.0.0/' /etc/redis.conf

WORKDIR /home/precondition
