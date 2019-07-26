<?php
require __DIR__ . '/../include/inc.php';
require __DIR__ . '/../include/class.docker.php';

try {
    $containers = [];
    list($status, $result) = $docker->query('/containers/json?all=1&size=1&filters={"label":["localcloud9"]}');

    if($status == 200) {
        $json = json_decode($result, true);
        $k    = strlen($_ENV['CONTAINER_PREFIX']) + 1;

        foreach($json as $item) {
            $name = substr($item['Names'][0], $k);
            $containers[$name] = [
              'State'  => $item['State'],
              'Status' => $item['Status'],
              'Size'   => $docker::sizeFormat($item['SizeRw'])
            ];
        }
        $json = null;
    }

} catch(Exception $e) {
    echo $e->getMessage();
}

$tpl->render('index', [
  'title'      => 'Workspaces',
  'theme'      => 'grey padding',
  'workspaces' => $db->data,
  'containers' => $containers
]);