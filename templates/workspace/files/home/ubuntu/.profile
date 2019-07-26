# ~/.profile: executed by the command interpreter for login shells.
# This file is not read by bash(1), if ~/.bash_profile or ~/.bash_login
# exists.
# see /usr/share/doc/bash/examples/startup-files for examples.
# the files are located in the bash-doc package.

# the default umask is set in /etc/profile; for setting the umask
# for ssh logins, install and configure the libpam-umask package.
#umask 022

export PS1="\u:\w$ "

# if running bash
if [ -n "$BASH_VERSION" ]; then
    # include .bashrc if it exists
    if [ -f "$HOME/.bashrc" ]; then
        . "$HOME/.bashrc"
    fi
fi

# Load Pyenv
if [ -d "$HOME/.pyenv" ]; then
    export PYENV_ROOT="$HOME/.pyenv"
    export PATH="$HOME/.pyenv/bin:$PATH"
    eval "$(pyenv init -)"
    eval "$(pyenv virtualenv-init -)"
fi

# hack: avoid slow npm sanity check in nvm
[ "$BASH_VERSION" ] && npm() {
    if [ "$*" == "config get prefix" ]; then
        which node | sed "s/bin\/node//";
    else
        $(which npm) "$@";
    fi
}

# Load nvm
[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" 

# Disable progress bars by default
# https://github.com/npm/npm/issues/11283
if which npm >/dev/null; then
    npm set progress=false

    # end hack
    unset npm
fi
