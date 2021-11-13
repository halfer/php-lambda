# Demo of a PHP-based lambda
#
# See example:
# https://github.com/aws-samples/php-examples-for-aws-lambda/blob/master/0.7-PHP-Lambda-functions-with-Docker-container-images/Dockerfile

FROM php:8.0-cli-alpine

WORKDIR /root

# Install Composer
COPY bin bin
RUN sh /root/bin/install-composer.sh
RUN php /root/composer.phar --version

# Install Composer deps
COPY composer.json composer.lock /root/
RUN php composer.phar install

# Install runtimes
COPY runtime /var/runtime
COPY src /var/task/

# Entrypoint
CMD ["php", "/var/runtime/bootstrap"]
