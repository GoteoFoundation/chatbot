#!/bin/sh
set -e

# Check migrate status
artisan migrate:status
if [ $? -ne 0 ]; then
    # try to migrate if unsuccessful
    artisan migrate --force
    artisan db:seed
fi

# check if admin user exists
if [ -z "$APP_KEY" ]; then
    artisan key:generate --show
fi
# force user owner for temp folders
chown www-data:www-data -R /var/www/html/

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

exec "$@"
