FROM php:8.1-apache AS app

ARG build_env='dev'
ENV APP_ENV=$build_env

#COPY config/apt.conf /etc/apt/apt.conf
COPY config/sources.list /etc/apt/sources.list
RUN apt-get update && apt-get install -y \
        redis \
        nano \
        curl \
        bash \
        git \
        wget \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
         zlib1g-dev \
            libzip-dev \
            zip \
    && docker-php-ext-install -j$(nproc) iconv mysqli pdo_mysql zip \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install xdebug-3.2.1 \
    && pecl install redis-5.3.7 \
    	&& docker-php-ext-enable redis xdebug

COPY config/gitconfig /etc/gitconfig
COPY ./*.ini /usr/local/etc/php/conf.d/
COPY config/apache2.conf /etc/apache2/site-available/homebrain.conf
RUN ln -s /etc/apache2/site-available/homebrain.conf /etc/apache2/conf-enabled/homebrain-apache2.conf && rm -rf /etc/apache2/sites-enabled/000-default.conf && rm -rf /etc/apache2/sites-available/000-default.conf && service apache2 restart
RUN a2enmod rewrite
#RUN apk --no-cache add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN useradd -Mr mysql && usermod -aG www-data mysql
RUN #useradd -Mr redis && usermod -aG www-data redis

RUN chown www-data:www-data /var/www/html/ -R && chmod 755 -R /var/www/html/
# Указываем рабочую директорию для PHP

COPY config/curlDownload.sh /usr/local/bin/curlDownload.sh
RUN chmod +x /usr/local/bin/curlDownload.sh
RUN /usr/local/bin/curlDownload.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN wget https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar && chmod +x pickle.phar && php pickle.phar install redis
WORKDIR /var/www/html
#CMD ["app"]