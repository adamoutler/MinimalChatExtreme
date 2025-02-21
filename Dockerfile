FROM alpine:3.16
ENV LANG C.UTF-8
WORKDIR /data


# Install system dependencies
RUN     apk update && \
            apk add --no-cache \
            git \
            nginx \
            php8-ctype \
            php8-fpm \
            php8-exif \
            php8-fileinfo \
            php8-gd \
            php8-iconv \
            php8-json \
            php8-ldap \
            php8-pdo_sqlite \
            php8-simplexml \
            php8-tokenizer

COPY    www.conf /etc/php8/php-fpm.d/zz-docker.conf

RUN     git clone https://github.com/adamoutler/MinimalChatExtreme /var/www/MinimalChatExtreme
RUN     mkdir -p /var/www /run/nginx/

RUN     rm -Rf /var/www/MinimalChatExtreme/rooms


COPY run.sh /
COPY    ingress.conf /etc/nginx/http.d/ingress.conf
RUN chmod a+x /run.sh
EXPOSE 8999
CMD ["/run.sh"]
