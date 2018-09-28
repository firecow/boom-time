FROM nanoninja/php-fpm:7.2

RUN echo "deb http://repo.mongodb.org/apt/debian stretch/mongodb-org/3.6 main" | tee /etc/apt/sources.list.d/mongodb-org-3.6.list && \
    apt-get update && apt-get install -my --allow-unauthenticated unzip mysql-client mongodb-org-shell mongodb-org-tools && rm -rf /var/lib/apt

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer