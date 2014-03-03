#!/bin/bash -ex

# enable specific php extensions
echo "extension=mongo.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
echo "extension=memcached.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
echo "extension=apc.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

# update password as Incenteev ParameterHandler, seems to ignore blank password
echo "SET PASSWORD FOR 'travis'@'localhost' = PASSWORD('sonata');" | mysql -u travis
echo "CREATE DATABASE sonata" | mysql -u travis -psonata

# tweak some php settings
echo "apc.shm_size=512M" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

# fix behat host
sed -i 's@demo.sonata-project.org@localhost/app.php@g' behat.yml.dist


# install nginx and setup php-fpm

sudo apt-get install nginx


CURRENT_PATH=`pwd`

echo "
worker_processes 2;

events {
    worker_connections 512;
}

http {
    include mime.types;
    default_type application/octet-stream;

    access_log off;

    gzip on;

    server {
        listen 80;

        root $CURRENT_PATH/web;

        location ~ \.php($|/) {

            include fastcgi_params;

            fastcgi_pass                  unix:/var/run/php5-fpm.sock;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            fastcgi_param PATH_INFO       \$fastcgi_script_name;
        }
    }
}
" | sudo tee /etc/nginx/nginx.conf

echo "
[global]
pid = /var/run/php5-fpm.pid
error_log = /var/log/php5-fpm.log

[www]
user = travis
group = travis

listen = /var/run/php5-fpm.sock

pm = static
pm.max_children = 8

php_admin_value[memory_limit] = 512M
" > ~/php-fpm.conf

# setup mock email (from http://michaelthessel.com/mock-mail-setup-for-travis-ci/)
sudo apt-get install -y -qq postfix
sudo service postfix stop
nohup smtp-sink -d "%d.%H.%M.%S" localhost:2500 1000 &
echo -e '#!/usr/bin/env bash\nexit 0' | sudo tee /usr/sbin/sendmail
echo 'sendmail_path = "/usr/sbin/sendmail -t -i "' | sudo tee /home/travis/.phpenv/versions/$(phpenv version-name)/etc/conf.d/sendmail.ini

# restart php-fpm and nginx
sudo ~/.phpenv/versions/$(phpenv version-name)/bin/php-fpm --fpm-config ~/php-fpm.conf
sudo /etc/init.d/nginx restart

# make sure we run with the last version of composer
wget https://getcomposer.org/composer.phar

# get the code and load fixtures
DATABASE_NAME=sonata DATABASE_USER=travis DATABASE_PASSWORD=sonata DATABASE_HOST=localhost php composer.phar install --dev --prefer-source
php load_data.php
