FROM 1of0/php-ci:7.0

WORKDIR /var/staging

ADD ./ ./

RUN composer install &> /dev/null
CMD phpunit --configuration phpunit.xml
