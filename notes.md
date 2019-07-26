# Notes

## c9 templates

c9 has several [workspace templates](https://c9.io/community/templates) out of the box

![](https://files.readme.io/cf2bd61-console-choose-template.png)

| Workspace tile caption  | Workspace template tile caption | Base image ID
|---                      |---                              |---
| php workspace           | HTML5                           | cloud9/ws-html5
| nodejs workspace        | Node.js                         | cloud9/ws-nodejs
| php workspace           | PHP, Apache, & MySQL            | cloud9/ws-php
| python workspace        | Python                          | cloud9/ws-python-plain
| python workspace        | Django                          | cloud9/ws-python
| ruby workspace          | Ruby                            | cloud9/ws-ruby
| cpp workspace           | C++                             | cloud9/ws-cpp
| wordpress workspace     | Wordpress                       | cloud9/ws-wordpress
| railstutorial workspace | Rails Tutorial                  | cloud9/ws-ruby
| &lt;blank&gt;           | Blank                           | cloud9/ws-default
| cs50 workspace          | Harvard's CS50                  | cs50/ide - Note that this image includes a special version of Cloud9 Core with CS50-specific plugins. For more information, see [CS50 IDE Offline](https://cs50.readthedocs.io/ide/offline/).

[Dockerfile of the templates](https://github.com/c9/templates)

All c9 templates build on top of [cloud9/workspace](https://github.com/c9/templates/blob/master/workspace/Dockerfile).  
It uses Ubuntu 14.04 and a bunch of stacks come pre-installed (ruby, php, c, etc): 

---

## Update the IDE theme

1. Start the IDE
2. Click on the gear icon (upper right)
3. Go to THEMES and select the theme you want
4. You can also add your own stylesheet

This is the one I like:

![](https://i.imgur.com/nSgHWKN.png)

It uses this stylesheet:

``` css
.c9terminal .c9terminalcontainer .terminal {
    background-color: #1A1A1A !important;
    font-size: 1.1em !important;
    color: white !important;
}
.terminal .ace_content {
    color: white !important;
}
.c9terminalcontainer {
    background-color: #1A1A1A !important;
}
.c9terminalFocus .c9terminalcontainer .terminal .reverse-video {
    background-color: white;
    color: black;
}

.editor_tab .btnsesssioncontainer,
.panel-bar {
    background-color: lavender;
}
.panel-bar {
    border-color: white;
}
.filetree .tree-row.selected {
    background: rgba(0,0,0,0.1);
}
.ace-cloud9-day .ace_invisible {
    color: rgb(191, 191, 191);
    opacity: 0.5;
}
```

---

## docker.sock

The app uses UNIX sockets to start containers on the host.  
To check that it works properly as follows:

* Access the shell

  ```
  cd localcloud9
  docker run --rm -it -v /var/run/docker.sock:/var/run/docker.sock localcloud9/php bash
  ```

* Check your curl version  
  `--unix-socket` requires curl > 7.40.

  ```
  curl --version
  ```

* Check your PHP version  
  `CURLOPT_UNIX_SOCKET_PATH` require PHP > 7.0.7.

  ```
  php --version
  ```

* Test the socket

  ```
  su - www-data -s /bin/bash -c 'curl --unix-socket /var/run/docker.sock http://v1.24/containers/json'
  ```

  [Engine API v1.24](https://docs.docker.com/engine/api/v1.24/)
