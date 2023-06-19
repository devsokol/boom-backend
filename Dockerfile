# syntax=docker/dockerfile:experimental
FROM 903510684835.dkr.ecr.eu-north-1.amazonaws.com/common/backend-base-image-php82:v1.0.4

ADD . /var/www/html

WORKDIR /var/www/html

RUN rm -rf /usr/local/etc/php-fpm.d/*

ADD .ci/nginx/default.conf  /etc/nginx/conf.d/default.conf
ADD .ci/nginx/nginx.conf    /etc/nginx/nginx.conf
ADD .ci/php/www.conf        /usr/local/etc/php-fpm.d/www.conf
ADD .ci/php/php.ini         /usr/local/etc/php/php.ini
#ADD .ci/php/10-opcache.ini  /usr/local/etc/php/conf.d/10-opcache.ini
ADD .ci/startup.sh          /usr/local/bin/startup.sh
ADD .ci/supervisor          /etc/supervisor

RUN mkdir -p /var/www/ssl && \
	openssl req \
	-x509 \
	-nodes \
	-days 365 \
	-newkey rsa:2048 \
	-out /var/www/ssl/nginx.crt \
	-keyout /var/www/ssl/nginx.key \
	-subj "/C=US/ST=IL/L= /O= /OU= /CN= "

RUN usermod -u 1000 nginx                  && \
	groupmod -g 1000 nginx                 && \
	mkdir -p /var/www/html/storage/logs    && \
	echo -e "" >  /var/www/html/storage/logs/laravel.log && \
	chmod -R 775 /var/www/html             && \
	chown -R nginx:nginx /var/www/html

USER nginx

# --no-interaction --no-dev --prefer-dist --optimize-autoloader
RUN /usr/local/bin/php -dmemory_limit=4G $(which composer) install --no-interaction --no-dev --prefer-dist --classmap-authoritative && \
	/usr/local/bin/php artisan storage:link && \
	/usr/local/bin/php artisan optimize && \
	/usr/local/bin/php artisan event:cache && \
	/usr/local/bin/php artisan l5-swagger:generate api_v1 && \
	/usr/local/bin/php artisan l5-swagger:generate mobile_v1 && \
	yes | /usr/local/bin/php artisan migrate

USER root

RUN chmod -R 775 /var/www/html             && \
	chmod -R 775 /usr/local/bin/startup.sh && \
	chown -R nginx:nginx /var/www/ssl      && \
	chown -R nginx:nginx /var/www/html     && \
	mkdir -p /etc/periodic/nginx           && \
	echo -e "* * * * * /usr/local/bin/php -dmemory_limit=2G /var/www/html/artisan schedule:run" >  /etc/periodic/nginx/cron && \
	crontab -u nginx /etc/periodic/nginx/cron

VOLUME /var/www/html

ENTRYPOINT ["/usr/bin/dumb-init", "--rewrite", "15:3", "--"]

CMD [ "/usr/local/bin/startup.sh" ]
