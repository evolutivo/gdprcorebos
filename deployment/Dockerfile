FROM alpine:3.7

RUN apk update && apk upgrade && apk add nginx bash supervisor curl git nano

RUN adduser -D -g 'www' www

RUN mkdir /www
RUN chown -R www:www /www

COPY deployment/nginx.conf /etc/nginx/

RUN mkdir -p /run/nginx
RUN mkdir -p /run/php

RUN echo "daemon off;" >> /etc/nginx/nginx.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

RUN apk add php7-fpm php7-mcrypt php7-soap php7-openssl php7-gmp php7-pdo_odbc php7-json \
  php7-dom php7-pdo php7-zip php7-mysqli php7-sqlite3 php7-apcu php7-pdo_pgsql php7-bcmath \
  php7-gd php7-odbc php7-pdo_mysql php7-pdo_sqlite php7-gettext php7-xmlreader php7-xmlrpc \
  php7-bz2 php7-iconv php7-pdo_dblib php7-curl php7-ctype php7-cli php7-session \
  php7-phar php7-tokenizer php7-mbstring php7-xmlwriter php7-xml php7-simplexml php7-fileinfo

COPY deployment/php7/php.ini /etc/php7/
COPY deployment/php7/php-fpm.conf /etc/php7/
COPY deployment/php7/php-fpm.d/www.conf /etc/php7/php-fpm.d/

RUN apk add tzdata
RUN ls /usr/share/zoneinfo
RUN cp /usr/share/zoneinfo/UTC /etc/localtime
RUN echo "UTC" > /etc/timezone

RUN chown -R www:www /var/lib/nginx
RUN chown -R www:www /var/tmp/nginx

EXPOSE 80

COPY deployment/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

COPY --chown=www:www . /www

WORKDIR /www

COPY deployment/start.sh .
RUN chmod +x start.sh
RUN rm -rf /www/deployment

CMD ["./start.sh"]

EXPOSE 80