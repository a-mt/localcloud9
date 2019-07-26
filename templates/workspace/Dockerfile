#c9 base template = https://github.com/c9/templates/blob/master/workspace/Dockerfile
FROM ubuntu:18.04

RUN apt-get update
RUN apt-get install -y curl git wget vim locate sudo

#----------------------------------------------------------
# CREATE USER

# Allow members of group sudo to execute any command
RUN sed -i 's/^%sudo\s*ALL=(ALL:ALL)\s*ALL/%sudo ALL=(ALL:ALL) NOPASSWD:ALL/' /etc/sudoers

# Define environment variables
ENV USER="ubuntu"
ENV HOME="/home/ubuntu"
ENV PS1="\u:\w$ "
# ENV PS1="\[\033[01;32m\]${C9_USER}\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]$ "

# Create user
RUN useradd -ms /bin/bash -d $HOME $USER \
    && usermod -aG sudo $USER

#----------------------------------------------------------
# INSTALL PYTHON

RUN apt-get install -y python3 python3-pip python3-openssl
RUN ln -s /usr/bin/python3 /usr/bin/python
RUN ln -s /usr/bin/pip3 /usr/bin/pip
RUN pip --no-cache-dir install virtualenv

# Install dependencies to build extra Python versions
# https://github.com/pyenv/pyenv/wiki
ENV DEBIAN_FRONTEND=noninteractive
ENV DEBCONF_NONINTERACTIVE_SEEN=true
RUN apt-get install -y --no-install-recommends tk-dev libssl-dev libreadline-dev libsqlite3-dev libbz2-dev

# Install Pyenv
USER $USER
RUN curl -L https://github.com/pyenv/pyenv-installer/raw/master/bin/pyenv-installer | bash

# pyenv install --list
# pyenv install 2.7.16
# pyenv versions
# pyenv local 2.7.16
# python --version

#----------------------------------------------------------
# INSTALL NODE

# Install Nvm
USER $USER
RUN wget -qO- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | sh -e

# Install Node
ENV NVM_DIR="$HOME/.nvm"
ENV NODE_LTS="10.15.3"

RUN . "$NVM_DIR/nvm.sh" \
        && nvm install $NODE_LTS \
        && nvm alias default $NODE_LTS \
        && nvm use $NODE_LTS

# nvm ls
# nvm install 11
# nvm use 11
# node --version

#----------------------------------------------------------
# MISC

USER root

# Heroku toolbelt
RUN curl https://cli-assets.heroku.com/install.sh | sh

# Gitl
RUN git clone https://github.com/lennartcl/gitl.git /opt/gitl

# Install locale en_US.UTF-8
RUN sudo apt-get install locales \
    && sudo locale-gen en_US.UTF-8 \
    && sudo update-locale

# Install util packages
RUN sudo apt-get install -y dialog net-tools

#----------------------------------------------------------
# USER CONFIGS

ENV PATH="$HOME/workspace/node_modules/.bin:$HOME/bin:$PATH:/opt/gitl"
#export PYTHONPATH=/usr/lib/python3.7/:/usr/lib/python3.7/site-packages

RUN rm -rf /tmp/*
ADD home/ubuntu $HOME/
RUN mkdir $HOME/bin
RUN chown -R $USER: $HOME
RUN touch ~/.sudo_as_admin_successful
RUN cd $HOME/.ssh && ssh-keygen -f id_rsa -t rsa -N ''

# Have the Docker container listen to these ports at run time.
EXPOSE 5050 8080 8081 8082
USER $USER
WORKDIR $HOME
LABEL localcloud9=1
CMD /bin/bash -l

# docker build --file Dockerfile -t localcloud9/workspace files
# docker run --rm -it localcloud9/workspace
