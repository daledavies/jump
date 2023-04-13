# This dockerfile is intended to built using buildx to enable multi-platform
# images...
# https://docs.docker.com/desktop/multi-arch/
# docker buildx create --name mybuilder
# docker buildx use mybuilder
# docker buildx inspect --bootstrap
# docker buildx build --platform linux/amd64,linux/arm64,linux/arm/v7 -t daledavies/jump:v1.1.3 --push .

# Start with the official composer image, copy application files and install
# dependencies.
FROM --platform=$BUILDPLATFORM composer AS builder
COPY jumpapp/ /app
RUN composer install --no-dev \
  --optimize-autoloader \
  --no-interaction \
  --no-progress

# Switch to base alpine image so we can copy application files into it.
FROM alpine:latest

WORKDIR /var/www/html

# Create a non-root user for running nginx and php.
RUN addgroup -S jumpapp && \
    adduser \
      --disabled-password \
      --ingroup jumpapp \
      --no-create-home \
      jumpapp

# Copy the built files from composer, chowning as jumpapp or they will
# be owned by root.
COPY --chown=jumpapp --from=builder /app /usr/src/jumpapp

# Install required packages.
RUN apk add --no-cache \
  bash \
  curl \
  nginx \
  php81 \
  php81-curl \
  php81-dom \
  php81-fileinfo \
  php81-fpm \
  php81-json \
  php81-opcache \
  php81-openssl \
  php81-session \
  php81-xml \
  php81-zlib

# Create symlink for anything expecting to use "php".
RUN ln -s -f /usr/bin/php81 /usr/bin/php

# Nginx config.
COPY docker/nginx.conf /etc/nginx/nginx.conf

# PHP/FPM config.
COPY docker/fpm-pool.conf /etc/php81/php-fpm.d/www.conf
COPY docker/php.ini /etc/php81/conf.d/custom.ini

COPY docker/entrypoint.sh /usr/local/bin/

# Create the cache directories and change owner of everything we need.
RUN mkdir -p /var/www/cache/application \
    && mkdir -p /var/www/cache/icons \
    && chown -R jumpapp:jumpapp /var/www/html /var/www/cache/icons \
    /var/www/cache/application \
    && chmod +x /usr/local/bin/entrypoint.sh

# Expose the port we configured for nginx.
EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping || exit 1
