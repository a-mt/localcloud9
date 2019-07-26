<?php

class Docker {
    private static $instance;
    private $version = 'http://v1.24';

    /**
     * Get singleton
     */
    public static function get() {
        if(!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param string $endpoint         - Ex: /containers/json
     * @param string[optional] $method - [GET]
     * @param string[optional] $args   - [array()] ex: ["Image":"nginx"]
     * @throws Exception               - You have to enable curl
     * @return array(string,string)    - Returns status + response
     */
    public function query($endpoint, $method = 'GET', $args = [])
    {
        if(!$ch = curl_init($this->version . $endpoint)) {
            throw new \Exception('You have to enable curl');
        }
        $stderr = fopen('php://temp', 'w+');

        curl_setopt_array($ch, array(
            CURLOPT_UNIX_SOCKET_PATH => '/var/run/docker.sock',
            CURLOPT_RETURNTRANSFER   => true,
            CURLOPT_FOLLOWLOCATION   => true,
            CURLOPT_CONNECTTIMEOUT   => 10,
            CURLOPT_TIMEOUT          => 50,
            CURLOPT_VERBOSE          => true,
            CURLOPT_STDERR           => $stderr
        ));
        if($method == 'POST') {
            $data_string = json_encode($args);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            ));
        } else if($method != 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); // DELETE
        }

        $response = curl_exec($ch);
        $status = curl_getinfo($ch)['http_code'];

        // Check cUrl response
        if($response === false) {
             $msg = sprintf("cUrl error (#%d): %s\n",
                curl_errno($ch),
                curl_error($ch)
            );
            rewind($stderr);

            $msg .= "\n" . stream_get_contents($stderr);
            throw new \Exception($msg);
        }
        curl_close($ch);

        return [$status, $response];
    }

    /**
     * @param integer $bytes
     * @return string
     */
    public static function sizeFormat($bytes, $precision = 0) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $mod   = 1000;
        $bytes = max($bytes, 0);

        if(!$bytes) {
            return '0B';
        }
        $pow    = floor(log($bytes) / log($mod));
        $pow    = min($pow, count($units) - 1);
        $bytes /= pow($mod, $pow);

        return round($bytes, $precision) . $units[$pow]; 
    }
}

$docker = Docker::get();

/**
-----------------------------------------------------------
ENDPOINTS
https://docs.docker.com/engine/api/v1.24/

GET /containers/json                    List containers
POST /containers/create                 Create a container
GET /containers/(id or name)/json       Inspect a container
GET /containers/(id or name)/top        List processes running in a container
GET /containers/(id or name)/logs       Get container logs
GET /containers/(id or name)/changes    Inspect changes on a container's filesystem
GET /containers/(id or name)/export     Export a container
GET /containers/(id or name)/stats      Get container resource usage
POST /containers/(id or name)/resize    Resize a container tty
POST /containers/(id or name)/start     Start a container
POST /containers/(id or name)/stop      Stop a container
POST /containers/(id or name)/restart   Restart a container
POST /containers/(id or name)/kill      Kill a container
POST /containers/(id or name)/update    Update a container
POST /containers/(id or name)/rename    Rename a container
POST /containers/(id or name)/pause     Pause a container
POST /containers/(id or name)/unpause   Unpause a container
POST /containers/(id or name)/attach    Attach to a container
GET /containers/(id or name)/attach/ws  Attach to a container (websocket)
POST /containers/(id or name)/wait      Wait a container
DELETE /containers/(id or name)         Remove a container
HEAD /containers/(id or name)/archive   Retrieve information about files in a container
GET /containers/(id or name)/archive    Get an archive of a resource in the container

POST /auth                              Check auth configuration
GET /info                               Display system-wide information
GET /version                            Show the Docker version
GET /_ping                              Ping the Docker server
GET /events                             Monitor docker's events

GET /images/json                        List images
POST /build                             Build image from a Dockerfile
POST /images/create                     Create an image by pulling it from the registry
GET /images/(name)/json                 Inspect an image
GET /images/(name)/history              Get the history of an image
POST /images/(name)/push                Push an image on the registry
POST /images/(name)/tag                 Tag an image into a repository
DELETE /images/(name)                   Remove an image
GET /images/search                      Search images
POST /commit                            Create a new image from a container
GET /images/(name)/get                  Get a tarball of an image in a repository
GET /images/get                         Get a tarball of all images in a repository
POST /images/load                       Load a tarball with a set of images and tags into Docker

POST /containers/(id or name)/exec      Set up an exec instance in a running container
POST /exec/(id)/start                   Start a previously set up exec instance
POST /exec/(id)/resize                  Resize the tty session used by the exec command
GET /exec/(id)/json                     Inspect the exec command

GET /volumes                            List volumes
POST /volumes/create                    Create a volume
GET /volumes/(name)                     Inspect a volume
DELETE /volumes/(name)                  Remove a volume

GET /networks                           List networks
GET /networks/(id or name)              Inspect network
POST /networks/create                   Create a network
POST /networks/(id or name)/connect     Connect a container to a network
POST /networks/(id or name)/disconnect  Disconnect a container from a network
DELETE /networks/(id or name)           Remove a network

GET /plugins                            List installed plugins
POST /plugins/pull?name=<plugin name>   Install a plugin
GET /plugins/(plugin name)              Inspect a plugin
POST /plugins/(plugin name)/enable      Enable a plugin
POST /plugins/(plugin name)/disable     Disable a plugin
DELETE /plugins/(plugin name)           Remove a plugin

GET /nodes                              List nodes
GET /nodes/(id or name)                 Inspect a node
DELETE /nodes/(id or name)              Remove a node
POST /nodes/(id)/update                 Update a node
GET /swarm                              Inspect swarm
POST /swarm/init                        Initialize a new swarm
POST /swarm/join                        Join an existing swarm
POST /swarm/leave                       Leave a swarm
POST /swarm/update                      Update a swarm
GET /services                           List services
POST /services/create                   Create a service
DELETE /services/(id or name)           Remove a service
GET /services/(id or name)              Inspect a service
POST /services/(id)/update              Update a service
GET /tasks                              List tasks
GET /tasks/(id)                         Inspect a task
*/