FROM ubuntu
# https://github.com/NatLibFi/Skosmos/wiki/Installation

RUN apt-get update -y && \
    apt-get install -y git php-gettext unzip vim wget php apache2 libapache2-mod-php curl php-curl php-mbstring  && \
	cd /tmp && wget https://github.com/NatLibFi/Skosmos/archive/v1.9.zip && \
	unzip v1.9.zip && rm v1.9.zip && \
    rm -r /var/www/html/* && \
    rmdir /var/www/html && \
    mv Skosmos-1.9 /var/www/html && cd /var/www/html

COPY vocabularies.ttl.dist /var/www/html/vocabularies.ttl.dist
COPY config.inc.dist /var/www/html/config.inc

RUN cd /var/www/html && \
    mv config.inc.dist config.inc && mv vocabularies.ttl.dist vocabularies.ttl && \
    wget https://getcomposer.org/composer.phar && \
    a2enmod rewrite && \
    a2enmod php7.0 && \
    php composer.phar install --no-dev && \
    apt-get update -y && \
    apt-get install -y openssh-server

RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php/7.0/apache2/php.ini
RUN sed -i "s/display_errors = Off/display_errors = On/" /etc/php/7.0/apache2/php.ini
RUN sed -i "s/display_startup_errors = Off/display_startup_errors = On/" /etc/php/7.0/apache2/php.ini
RUN sed -i "s/error_reporting = .*$/error_reporting = E_ERROR | E_WARNING | E_PARSE/" /etc/php/7.0/apache2/php.ini

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

VOLUME ["/var/www/html", "/etc/apache2/sites-enabled"]

ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

CMD /usr/sbin/apache2ctl -D FOREGROUND

