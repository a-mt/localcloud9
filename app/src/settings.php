<?php
require __DIR__ . '/../include/inc.php';

$name = @$_GET['name'];
$w    = $name ? $db->search('name', $name) : null;

if(!$w) {
  header('Location: /');
  exit;
}

if(isset($_POST['submit'])) {
  $w['description'] = trim(@$_POST['description']);
  $db->update($w);
  $db->save();
  header('Location: /about/' . $name);
  exit;
}

if(isset($_POST['delete'])) {
  require __DIR__ . '/../include/class.docker.php';

  $container_name = $_ENV['CONTAINER_PREFIX'] . $name;

  // Delete container and volume
  list($status, $result) = $docker->query("/containers/$container_name/kill", "POST");
  list($status, $result) = $docker->query("/containers/$container_name", "DELETE");
  list($status, $result) = $docker->query("/volumes/$container_name", "DELETE");

  // Delete from database
  $db->delete($w);
  $db->save();
  header('Location: /');
  exit;
}

$tpl->render('settings', [
  'title' => $name,
  'name'  => $name,
  'w'     => $w
]);