#!/usr/bin/env bash
set -Eeuo pipefail

if [ -z "${DEVELOPMENT-}" ]; then
    echo >&2 "-------------------------------------------------------------"

    echo >&2 "- Repopulating web root with application files."
    if [ "$(ls -A /var/www/html)" ]; then
        rm /var/www/html/* -r
    fi
    cp /usr/src/jumpapp/* /var/www/html -r

    echo >&2 "- Check if backgrounds or sites volumes have been mounted."
    if [ -e "/backgrounds" ]; then
        echo >&2 "   - Backgrounds directory is mapped... symlinking."
        rm /var/www/html/assets/backgrounds -r
        ln -s /backgrounds /var/www/html/assets/
        if [ ! "$(ls -A /backgrounds)" ]; then
            echo >&2 "     -- Empty so populating with default files."
            cp /usr/src/jumpapp/assets/backgrounds/* /backgrounds -r
        fi
    fi

    if [ -e "/sites" ]; then
        echo >&2 "   - Sites directory is mapped... symlinking."
        rm /var/www/html/sites -r
        ln -s /sites /var/www/html/
        if [ ! "$(ls -A /sites)" ]; then
            echo >&2 "     -- Empty so populating with default files."
            cp /usr/src/jumpapp/sites/* /sites -r
        fi
    fi

    echo >&2 "- All done! Starting nginx/php services now."
    echo >&2 "-------------------------------------------------------------"
fi

php-fpm8
nginx -g 'daemon off;'
