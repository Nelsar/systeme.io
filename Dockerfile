FROM php:8.2-fpm
ENV TIMEZONE=Asia/Qyzylorda
#ARG TIMEZONE=Asia/Almaty


RUN apt-get update && apt-get install -y \
        gnupg \
        g++ \
        procps \
        openssl \
        git \
        unzip \
        zlib1g-dev \
        libzip-dev \
        libicu-dev  \
        libonig-dev \
        libxslt1-dev \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
        libmcrypt-dev \
        libpq-dev \
        acl \
        postgresql-client-15 \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
	&& docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql mbstring
    
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /app
COPY . /app

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY composer.json composer.json
COPY .env .env

#RUN composer remove doctrine/doctrine-migrations-bundle

EXPOSE 8000
CMD  php -S 0.0.0.0:8000 -t public/