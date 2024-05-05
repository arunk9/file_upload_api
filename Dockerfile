FROM php:8.1-alpine

WORKDIR /tmp

RUN apk --update --no-cache add \
  git \
  bash \
  libintl \
  icu-dev \
  icu-data-full \
  zlib-dev \
  libpng-dev \
  sqlite-dev \
  libzip-dev \
  libxml2-dev \
  libxslt-dev \
  libgomp \
  linux-headers\
  imagemagick imagemagick-dev \
  oniguruma-dev \
  openssh-client \
  rsync

RUN curl -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar \
  && chmod +x /usr/local/bin/composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli

RUN docker-php-ext-configure intl \
  && docker-php-ext-install -j "$(nproc)" \
  pdo \
  pdo_mysql \
  gd \
  opcache \
  intl \
  zip \
  calendar \
  dom \
  mbstring \
  zip \
  gd \
  xsl \
  soap \
  sockets \
  exif \
  bcmath

RUN docker-php-source extract \
    && apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS


RUN  apk del .phpize-deps-configure \
    && docker-php-source delete \
    && rm -rf /tmp/* \
        /usr/includes/* \
        /usr/share/man/* \
        /usr/src/* \
        /var/cache/apk/* \
        /var/tmp/*

RUN echo "memory_limit=1G" > /usr/local/etc/php/conf.d/zz-conf.ini

# Set the working directory in the container
WORKDIR /app

# Copy the Symfony application code
COPY . .

# Expose port 8000 to the outside world
EXPOSE 8000

# Start the Symfony application using the built-in server
CMD ["symfony", "server:start", "--no-tls", "--port=8000", "--allow-http"]