<?php
require __DIR__ . '/../include/inc.php';

$name = @$_GET['name'];
$w    = $name ? $db->search('name', $name) : null;

if(!$w) {
  header('Location: /');
  exit;
}

// Status
require __DIR__ . '/../include/class.docker.php';
try {
    // Workspace size: `sudo du -hs /var/lib/docker/volumes/ localcloud9_test/_data | cut -f1`

    list($status, $result) = $docker->query('/containers/json?all=1&size=1&filters={"volume":["' . $_ENV['CONTAINER_PREFIX'] . $name . '"]}');

    if($status == 200) {
        if($item = json_decode($result, true)[0]) {
          $w['container'] = [
              'State'    => $item['State'],
              'Status'   => $item['Status'],
              'Size'     => $docker::sizeFormat($item['SizeRw']),
              'SizeRoot' => $docker::sizeFormat($item['SizeRootFs'])
            ];
        }
    }

} catch(Exception $e) {
    echo $e->getMessage();
}

$tpl->render('about', [
  'title' => $name,
  'name'  => $name,
  'w'     => $w
]);