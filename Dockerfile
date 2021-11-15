# Demo of a PHP-based lambda
#
# See example:
# https://github.com/aws-samples/php-examples-for-aws-lambda/blob/master/0.7-PHP-Lambda-functions-with-Docker-container-images/Dockerfile

FROM php:8.0-cli-alpine AS builder

WORKDIR /root

# Install Composer
COPY bin bin
RUN sh /root/bin/install-composer.sh
RUN php /root/composer.phar --version

# Install Composer deps
COPY composer.json composer.lock /root/
RUN php /root/composer.phar install --no-dev

# Here's the run-time build (minus Composer)
FROM php:8.0-cli-alpine

# Update operating system
RUN apk update && apk upgrade

# Move deps to /opt, /root has significant permission issues
COPY --from=builder /root/vendor /opt/vendor

# Install runtimes
COPY runtime/bootstrap /var/runtime/
COPY task/index.php /var/task/

RUN chmod 777 /usr/local/bin/php /var/task/* /var/runtime/*

# The entrypoint seems to be the main handler
# and the CMD specifies what kind of event to process
WORKDIR /var/task
ENTRYPOINT ["/var/runtime/bootstrap"]
CMD ["index"]
