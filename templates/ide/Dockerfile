FROM localcloud9/workspace
USER $USER

#----------------------------------------------------------
# C9SDK

# Download Cloud9 Core
RUN sudo git clone https://github.com/c9/core.git /var/c9sdk \
    && sudo chown -R $USER: /var/c9sdk

SHELL ["/bin/bash", "-c"]

# Install Python 2.7.16
RUN source $HOME/.profile \
    && pyenv install 2.7.16

# Install Cloud9 Core
RUN source $HOME/.profile \
    && pyenv local 2.7.16 \
    && cd /var/c9sdk \
    && scripts/install-sdk.sh

# docker build --file Dockerfile -t localcloud9/ide .
# docker run --rm -it localcloud9/ide bash -l