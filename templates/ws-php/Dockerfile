FROM localcloud9/workspace
USER root

#----------------------------------------------------------
# APACHE

RUN apt-get update && \
    apt-get install -y apache2 libapache2-mod-php mysql-server \
      php php-mysql php-cli php-gd php-xsl php-curl php-soap \
      php-json php-gettext php-mbstring php-zip php-ldap

# Update conf files
SHELL ["/bin/bash", "-c"]

RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf
RUN a2enmod headers rewrite allowmethods
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

USER $USER

# docker build --file Dockerfile -t localcloud9/ws-php files
# docker run --rm -it localcloud9/ws-php bash -l
