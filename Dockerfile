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
# Move deps to /opt, /root seems to have permission issues
RUN php /root/composer.phar install && \
    mv /root/vendor /opt/vendor

# Install runtimes
COPY runtime/bootstrap /var/task/
COPY src/index.php /var/task/

#RUN find /var -type f -exec chmod 644 {} +
#RUN find /usr -type f -exec chmod 744 {} +
#RUN find /var -type d -exec chmod 755 {} +
#RUN find /usr -type d -exec chmod 755 {} +

RUN mv /usr/local/bin/php /var/task/

RUN chmod 777 /var/task/*

# Entrypoint
WORKDIR /var/task
ENTRYPOINT []
CMD ["/var/task/php", "/var/task/bootstrap"]
