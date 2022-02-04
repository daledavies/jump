# Start with the official composer image, copy application files and install
# dependencies.
FROM composer AS composer
COPY web/ /app
RUN composer install --no-dev \
  --optimize-autoloader \
  --no-interaction \
  --no-progress

# Switch to trafex/php-nginx image and copy application files into it.
FROM trafex/php-nginx
COPY --chown=nginx --from=composer /app /var/www/html

# The trafex/php-nginx image runs as "nobody" user so we need to switch to root
# so we can make changes inside the container.
USER root

# We need the following PHP extensions.
RUN apk add php8-fileinfo

# Create the cache directories.
RUN mkdir -p /var/www/cache/application \
    && chown nobody:nobody /var/www/cache/application \
    && mkdir -p /var/www/cache/icons \
    && chown nobody:nobody /var/www/cache/icons

# Switch back to the nobody user so we're not running as root forever.
USER nobody