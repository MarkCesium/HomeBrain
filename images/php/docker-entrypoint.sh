#!/bin/bash
set -e

#XDEBUG_EXTENSION_FILE="/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini"
PHP_PROD_INI="/usr/local/etc/php/conf.d/php.prod.ini"
PHP_DEV_INI="/usr/local/etc/php/conf.d/php.dev.ini"

echo "ENV is $APP_ENV"

if [[ "$APP_ENV" = "dev" ]]; then
#    echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > $XDEBUG_EXTENSION_FILE
    mv ${PHP_PROD_INI}{,.disabled} 2> /dev/null || true
    mv ${PHP_DEV_INI}{.disabled,} 2> /dev/null || true
else
#    rm -f $XDEBUG_EXTENSION_FILE
    mv ${PHP_DEV_INI}{,.disabled} 2> /dev/null || true
    mv ${PHP_PROD_INI}{.disabled,} 2> /dev/null || true
fi

# Create all necessary folders

APP_DIRS=("var")

for path in ${APP_DIRS[*]}; do
    if [ ! -d "$path" ]; then
        echo "[bootstrap] Creating $path folder"
        mkdir -p "$path"
    fi

    if [ $(ls -ld $path | awk '{print $3}' | tail -1) != "www-data" ]; then
        echo "[bootstrap] Changing $path folder owner"
        chown -R www-data:www-data "$path"
    fi
done
exit 0