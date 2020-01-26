FROM php:7.4.1-apache

RUN apt-get update

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'baf1608c33254d00611ac1705c1d9958c817a1a33bce370c0595974b342601bd80b92a3f46067da89e3b06bff421f182') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN docker-php-ext-install pdo pdo_mysql
RUN service apache2 restart
#ADD ./isubat-app /var/www/html
WORKDIR /var/www/html
#RUN composer require server ^4.4.2
#RUN php bin/console server:run 0.0.0.0:8000