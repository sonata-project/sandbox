#!/usr/bin/env bash

yes yes | pecl install channel://pecl.php.net/apcu-4.0.7

# tweak php configuration
rm $HOME/.phpenv/versions/5.5.25/etc/conf.d/xdebug.ini
echo "apc.shm_size=512M" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

# dump php configuration
php -i

# retrieve plug and play webserver
wget https://github.com/mholt/caddy/releases/download/v0.7.1/caddy_linux_amd64.zip
unzip caddy_linux_amd64.zip
rm README.txt CHANGES.txt

# Configure webserver
echo "localhost:2015

root web

fastcgi / 127.0.0.1:9000 {
    ext   .php
    split .php
    index app.php
}

" > Caddyfile


# Start composer
DATABASE_NAME=test DATABASE_USER=$MYSQL_USER DATABASE_PASSWORD=$MYSQL_PASSWORD DATABASE_HOST=localhost php -d memory_limit=-1 $HOME/bin/composer install --prefer-source --no-interaction
$HOME/bin/composer dump-autoload -o

# Load data
php bin/load_data.php

# Configure Behat
echo "default:
    extensions:
        Behat\MinkExtension\Extension:
            base_url:  'http://localhost:2015/'
            goutte:     ~
            #selenium2: ~
            files_path: .

    context:
        parameters:
            base_url:  'http://localhost:2015/'
            files_path: .

    filters:
        tags: \"~@skipped&&~@api\"

api:
    paths:
        features:  features/api
        bootstrap: %behat.paths.features%/bootstrap
    filters:
        tags: \"~@skipped&&@api\"
wip:
    filters:
        tags: \"@skipped\"
" > behat.yml

# Tweak config_prod.yml

echo "imports:
    - { resource: config.yml }

framework:
    validation:
        cache:                         apc

doctrine:
    orm:
        entity_managers:
            default:
                metadata_cache_driver: apc
                query_cache_driver:    apc
                result_cache_driver:   apc

monolog:
    handlers:
        main:
            type:         stream
            path:         %kernel.logs_dir%/%kernel.name%_%kernel.environment%.log
            level:        debug
" > app/config/config_prod.yml