# localcloud9

Run the Cloud9 locally.

---

## Prerequisites: Docker

1. [Install Docker](https://docs.c9.io/v1.0/docs/migrating-to-cloud9-offline#section-step-2-install-docker)
2. [Start Docker](https://docs.c9.io/v1.0/docs/migrating-to-cloud9-offline#section-step-3-start-docker)
3. [Install Docker Compose](https://docs.docker.com/compose/install/)

---

## Install: localcloud9 templates

The app doesn't use c9 templates but its own, I wanted
* a distribution that is more recent.  
  To be able to use newer packages (curl > 7.40 to support sockets)
* a lighter image.  
  Most of the things that are installed in the c9 base template are things I never use

### Build the workspace

The base template builds on top of Ubuntu 18.04  
It contains Python (+ Pyenv), Node (+ Nvm) and some utilities (heroku, gitl, openssl, etc).

Build the image:

```
cd templates/workspace
docker build --file Dockerfile -t localcloud9/workspace files
docker run --rm -it localcloud9/workspace
```

ws-php builds on top of workspace and installs Lamp

```
cd templates/ws-php
docker build --file Dockerfile -t localcloud9/ws-php files
docker run --rm -it localcloud9/ws-php
```

### Build the IDE

* Build the IDE (needs `localcloud9/workspace`):

  ```
  cd templates/ide
  docker build --file Dockerfile -t localcloud9/ide .
  ```

* Test out the IDE:

  ```
  docker run --rm -it localcloud9/ide bash -l
  bash -lc '/home/ubuntu/.c9/node/bin/node /var/c9sdk/server.js -w /home/ubuntu/workspace --auth : --listen 0.0.0.0 --port 5050'
  ```

* Retrieve the IDE binaries (to be able to run the IDE on any Ubuntu image):

  ```
  cd localcloud9
  mkdir volumes
  docker run --rm -itd localcloud9/ide bash -l
  CONTAINER_ID=`docker ps --filter=ancestor=localcloud9/ide --format "{{.ID}}"`
  docker cp $CONTAINER_ID:/home/ubuntu/.c9 volumes
  docker cp $CONTAINER_ID:/var/c9sdk volumes
  docker stop $CONTAINER_ID
  mkdir volumes/db
  sudo chown 33:33 volumes/db
  ```

* Test out the IDE on `localcloud9/workspace`:

  ```
  cd localcloud9
  docker run -it --rm \
      -p 5050:5050 \
      -p 8080-8082:8080-8082 \
      --volume "$(pwd)/volumes/.c9:/home/ubuntu/.c9" \
      --volume "$(pwd)/volumes/c9sdk:/var/c9sdk" \
      localcloud9/workspace \
      bash -lc '/home/ubuntu/.c9/node/bin/node /var/c9sdk/server.js -w /home/ubuntu/workspace --auth : --listen 0.0.0.0 --port 5050'
  ```

  Note: to protect the web interface with a password, you can set the `auth` option

  ```
  node server.js -w /home/ubuntu/workspace --auth bob:pass --listen 0.0.0.0 --port 5050
  ```

  Go to http://localhost:5050

* If everything works, you can get rid the `localcloud9/ide` image.  
  We only needed it to build the binaries.

* Update the `VOLUME_DIR` path in `docker-compose.yml` with your own path (`$(pwd)/volumes`)

### Build the PHP image

This image is used by the app

```
cd templates/php
DOCKER_GUID=$(getent group docker | cut -d: -f3)
docker build --file Dockerfile --build-arg DOCKER_GUID=$DOCKER_GUID -t localcloud9/php .
```

### Configure the network

* Create a private network for `nginx-proxy`

  ```
  docker network create nginx-proxy
  ```
---

## Run the app

```
cd localcloud9
docker-compose up -d
```

Go to http://localhost
