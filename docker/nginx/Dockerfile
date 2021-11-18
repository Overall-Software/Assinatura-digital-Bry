FROM webdevops/php-nginx:8.0
COPY vhost.conf /opt/docker/etc/nginx
COPY php.ini /opt/docker/etc/php/

RUN apt-get update \
    && curl -sL https://deb.nodesource.com/setup_12.x | bash \
    && apt-get install -y \
        fonts-roboto \
        nodejs \
        curl \
        libxrender1 \
        libjpeg62-turbo \
        fontconfig \
        libxtst6 \
        xfonts-75dpi \
        xfonts-base \
        xz-utils

RUN npm install -g yarn
RUN curl "https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.buster_amd64.deb" -L -o "wkhtmltopdf.deb"
RUN dpkg -i wkhtmltopdf.deb

WORKDIR /app
