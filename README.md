# localcloud9

Run Cloud9 locally.

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

### Build the base workspace

The base template builds on top of Ubuntu 18.04  
It contains Python (+ Pyenv), Node (+ Nvm) and some utilities (heroku, gitl, openssl, etc).

Build the image:

```
cd templates/workspace
docker build --file Dockerfile -t localcloud9/workspace files
docker run --rm -it localcloud9/workspace
```

### Build the PHP workspace

ws-php builds on top of the base workspace. It adds Lamp

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

This image is used by the app.
This is the regular `php:fpm` image, except `www-data` has the right to use `/var/run/docker.sock` (to call the Docker API that is)

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

* Start the app

  ```
  cd localcloud9
  docker-compose up -d
  ```

* Go to http://cloud9.localhost

* To stop the app:

  ```
  docker-compose down
  ```

---

## Use https instead of http (optional)

In order to create a wildcard SSL certificate, that will work for the main app and for the started containers, you have to use a domain name with a top level domain (hostname.toplevel) — you can't use localhost (or any hostname on its own). This is to avoid wildcard certificates like `*.com` or `*.net`. Source: [Wildcard `*.localhost` SSL with Nginx and Chrome](https://serverfault.com/questions/811111/wildcard-localhost-ssl-with-nginx-and-chrome/957609)

I will use `cloud9.localhost`. To use a different top level, you will have to configure [dnsmasq](https://serverfault.com/questions/118378/in-my-etc-hosts-file-on-linux-osx-how-do-i-do-a-wildcard-subdomain#answer-569936)/

Note that if you change the domain name the app uses, the containers you previously started with it won't work anymore.
You will have to stop and remove the containers first — do it from the command line if you don't want to remove the volumes and be able to recreate the containers using the same data you previously had in the workspace.

* Define the configurations for your SSL certificate

  ```
  mkdir nginx/ssl && cd nginx/ssl
  ```

  ```
  cat <<EOF > ssl.conf
  [ req ]
  default_bits       = 2048
  distinguished_name = req_distinguished_name
  req_extensions     = req_ext

  [ req_distinguished_name ]
  countryName                 = Country Name (2 letter code)
  countryName_default         = GB
  stateOrProvinceName         = State or Province Name (full name)
  stateOrProvinceName_default = England
  localityName                = Locality Name (eg, city)
  localityName_default        = Brighton
  organizationName            = Organization Name (eg, company)
  organizationName_default    = Hallmarkdesign
  commonName                  = Common Name (e.g. server FQDN or YOUR name)
  commonName_max              = 64
  commonName_default          = cloud9.localhost

  [ req_ext ]
  subjectAltName = @alt_names

  [ alt_names ]
  DNS.1   = cloud9.localhost
  DNS.2   = *.cloud9.localhost
  EOF
  ```

* Create a Certificate Authority certificate (private key + self-signed certificate)

  ```
  openssl req -x509 \
    -nodes -sha256 -newkey rsa:2048 \
    -keyout CA.key \
    -out CA.crt \
    -days 1825 \
    -config ssl.conf \
    -subj "/C=xx/ST=x/L=x/O=x/OU=x/CN=cloud9.localhost"
  ```

* Create a server certificate

  * Create a private key

    ```
    openssl genrsa -out Server.key 2048
    ```

  * Create a Certificate Signing Request

    ```
    openssl req \
      -sha256 -new \
      -key Server.key \
      -out Server.csr \
      -config ssl.conf \
      -subj "/C=xx/ST=x/L=x/O=x/OU=x/CN=cloud9.localhost"
    ```

  * Create a certificate (signed with your Certificate Authority)

    ```
    openssl x509 -req \
      -sha256 \
      -extensions req_ext -extfile ssl.conf \
      -in Server.csr \
      -CA CA.crt \
      -CAkey CA.key \
      -CAcreateserial \
      -out Server.crt \
      -days 365
    ```

    Note: To view the content of a certificate: `openssl x509 -in Server.crt -text -noout`

* Create a Diffie-Hellman group

  ```
  openssl dhparam -out dhparam.pem 2048
  ```

* Go back to the root of the project

  ```
  cd ../..
  ```

* Edit docker-compose.yml

  ``` diff
  diff --git a/docker-compose.yml b/docker-compose.yml
  index 7bd362f..a7fe5e8 100644
  --- a/docker-compose.yml
  +++ b/docker-compose.yml
  @@ -5,6 +5,7 @@ services:
       image: jwilder/nginx-proxy
       ports:
         - "80:80"
  +      - "443:443"
         - "5050:5050"
         - "8080:8080"
         - "8081:8081"
  @@ -13,8 +14,12 @@ services:
         - ./nginx/nginx.tmpl:/app/nginx.tmpl
         - ./nginx/proxy_vhost.conf:/etc/nginx/vhost.d/default
         - /var/run/docker.sock:/tmp/docker.sock:ro
  +      - ./nginx/ssl/Server.key:/etc/nginx/certs/cloud9.localhost.key
  +      - ./nginx/ssl/Server.crt:/etc/nginx/certs/cloud9.localhost.crt
  +      - ./nginx/ssl/dhparam.pem:/etc/nginx/dhparam/dhparam.pem
  ```