#!/bin/sh

if [ -z "$SKIP_CONFIGURATION" ]; then
    # Check migrate status
    artisan migrate:status

    if [ $? -ne 0 ]; then
        # wait for database first
        echo -e "\e[31mMaybe database is not ready yet, waiting..."
        wait-for $DB_HOST:$DB_PORT -t 60 -- echo -e "\e[33mDatabase ready!"
        artisan migrate:status

        if [ $? -ne 0 ]; then
            echo -e "\e[31mLooks like database is not migrated, trying to setup application..."
            # try to migrate if unsuccessful
            artisan migrate --force
            artisan db:seed --force
            artisan register:admin --name="$ADMIN_NAME" --email="$ADMIN_EMAIL" --password="$ADMIN_PASSWORD"
        fi
    fi
fi

# force user owner for temp folders
chown www-data:www-data -R /var/www/html/

set -e
# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

exec "$@"
