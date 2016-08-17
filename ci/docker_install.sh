#!/bin/bash

[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

set -xe

apk add --no-cache git unzip curl

curl --location --output /usr/local/bin/phpunit https://phar.phpunit.de/phpunit.phar
chmod +x /usr/local/bin/phpunit

curl --location --output /usr/local/bin/composer https://getcomposer.org/composer.phar
chmod +x /usr/local/bin/composer

echo "date.timezone = UTC" >> /usr/local/etc/php/conf.d/test.ini

pecl install xdebug
docker-php-ext-enable xdebug
