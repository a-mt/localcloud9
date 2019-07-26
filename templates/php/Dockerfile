FROM php:fpm
ARG DOCKER_GUID=999

WORKDIR /var/www/app

# Allow www-data to use /var/run/docker.sock
RUN groupadd ping -g $DOCKER_GUID && \
     usermod -aG ping www-data

# docker build --file Dockerfile --build-arg DOCKER_GUID=$(getent group docker | cut -d: -f3) -t localcloud9/php .
# docker run --rm -it localcloud9/php bash