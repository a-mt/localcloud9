<?php
require __DIR__ . '/../include/inc.php';
require __DIR__ . '/../include/class.docker.php';
set_time_limit(0);
ini_set("zlib.output_compression", 0);  // off
ini_set("implicit_flush", 1);  // on
ini_set("display_errors", 1);
error_reporting(E_ALL);

$name = @$_GET['name'];
$w = $name ? $db->search('name', $name) : null;

if(!$w) {
    header('Location: /');
    exit;
}

$container_name = $_ENV['CONTAINER_PREFIX'] . $name;

//---------------------------------------------------------

function _echo($msg, $status = null, $txt = null) {
    echo '<script>' .
            ($status !== null ? 'document.querySelector(".status").innerHTML = `' . str_replace('\\', '\\\\', $status) . '`;'  : '') .
            'document.querySelector(".cool-message").innerHTML = `' . str_replace('\\', '\\\\', $msg) . '`;' .
            ($txt !== null ? 'document.querySelector(".msg").innerHTML = `' . str_replace('\\', '\\\\', $txt) . '`;' : '') .
        '</script>' . "\n";
    while(@ob_flush());
    flush();
}

function _render($body) {
    echo '<script>document.body.innerHTML = `' . str_replace('\\', '\\\\', $body) . '`</script>';
    while(@ob_flush());
    flush();
}

try {
    $state = 0;

    //+------------------------------------------------------
    //| CHECK IF THE CONTAINER EXISTS
    //| curl --unix-socket /var/run/docker.sock http://v1.24/containers/(id or name)/json
    //+------------------------------------------------------

    // Display temp loading screen
    header('Content-Encoding: none;');
    header('X-Accel-Buffering: no');
    header('Content-type: text/html; charset=utf-8');

    $tpl->render('ws', ['title' => $name], false);
    while(@ob_flush());
    flush();

    list($status, $result) = $docker->query("/containers/$container_name/json");

    //+------------------------------------------------------
    //| CREATE THE CONTAINER
    //| curl -XPOST --unix-socket /var/run/docker.sock -d '{"Image":"myimage"}' -H 'Content-Type: application/json' http://v1.24/containers/create
    //+------------------------------------------------------

    $auth   = @$w['auth'] ? $w['auth'] : ':';
    $domain = @$_ENV['DOMAIN_PREFIX'] . $name . '.' . $_ENV['DOMAIN'];

    if($status == 404) {
        _echo('Creating Your New Workspace');

        list($status, $result) = $docker->query('/containers/create?name=' . $container_name, 'POST', [
            'Image'      => $w['dockerBaseImage'],
            'Labels'     => ['localcloud9' => "1"],
            'HostConfig' => ['Binds' => [
                                $_ENV['VOLUME_DIR'] . '/.c9:/home/ubuntu/.c9',
                                $_ENV['VOLUME_DIR'] . '/c9sdk:/var/c9sdk',
                                $container_name . ':/home/ubuntu/workspace'
                            ],
                            'MemoryReservation' => 2147483648, // 2G Soft limit
                            'Memory'            => 2362232012, // 2.2G Hard limit
                            'CpuPeriod'         => 100000,     // Limit to 1 CPU
                            'CpuQuota'          => 100000,
            ],
            'NetworkingConfig' => [
                'EndpointsConfig' => [
                    'nginx-proxy' => [
                        'IPAMConfig' => null,
                        'Links'      => null,
                        'Aliases'    => null
                    ]
                ]
            ],
            'Env' => ["VIRTUAL_HOST=${domain}", 'VIRTUAL_PORT=8080', 'EXPOSE_PORT=5050,8080,8081,8082'],
            'Cmd' => ['bash', '-lc', "/home/ubuntu/.c9/node/bin/node /var/c9sdk/server.js "
                                        . "-w /home/ubuntu/workspace "
                                        . "--auth ${auth} "
                                        . "--listen 0.0.0.0 --port 5050"],
            'tty' => true
        ]);
        if($status >= 300) {
            _echo('Something went wrong', $status, '<pre>' . print_r($result, true) . '</pre>');
            die;
        }
        $state = 1;
    }
    $json = json_decode($result, true);

    //+------------------------------------------------------
    //| START THE CONTAINER
    //| curl -XPOST --unix-socket /var/run/docker.sock http://localhost/containers/(id)/start
    //+------------------------------------------------------

    if($state == 1
        || $json['State']['Status'] == 'created'
        || ($json['State']['Status'] == 'exited' && in_array($json['State']['ExitCode'], [255, 130, 137]))) {
        _echo('Starting The Workspace');
        list($status, $result) = $docker->query("/containers/$container_name/start", 'POST');

        if($status < 300) {
            list($status, $result) = $docker->query("/containers/$container_name/json");
            $json = json_decode($result, true);
            _echo('Starting The IDE');
            sleep(7);

            echo '<script>window.location.reload();</script>' . "\n";
            while(@ob_flush());
            flush();
            die;
        }
    }

    //+------------------------------------------------------
    //| DISPLAY THE CONTAINER
    //+------------------------------------------------------

    if($json['State']['Status'] == 'running') {
        //$url = $json['NetworkSettings']['IPAddress'] . ':5050';

        _render('<iframe src="//' . $domain . ':5050" frameborder="0" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%"></iframe>');
        die;
    }

    //+------------------------------------------------------
    //| DISPLAY THE LOGS
    //+------------------------------------------------------

    list($status, $result) = $docker->query("/containers/$container_name/logs?stderr=1&stdout=1&tail=100");

    $regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
    _echo(
        'Something went wrong',
        $status,
        '<pre>' . print_r($json, true) . '</pre>' .
        '<pre style="overflow: auto; padding: 30px 15px; background: #f5f5f5;">' . preg_replace($regex, '$1', $result)
    );
    die;

} catch(Exception $e) {
    echo '<pre>' . $e->getMessage();
}