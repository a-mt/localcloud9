<?php
require __DIR__ . '/../include/inc.php';

if(isset($_POST['submit'])) {

  $w = [];
  $w['name']            = trim(@$_POST['name']);
  $w['description']     = trim(@$_POST['description']);
  $w['auth']            = trim(@$_POST['auth']);
  $w['workspaceType']   = trim(@$_POST['template']);

  switch($w['workspaceType']) {
    case 'php':
      $w['dockerBaseImage'] = 'localcloud9/ws-php'; break;
    default:
      $w['workspaceType']   = 'default';
      $w['dockerBaseImage'] = 'localcloud9/workspace';
  }

  if($db->search('name', $w['name'])) {
    $_SESSION['err'] = $w['name'] . ' already exists';

    header('Location: /new');
    exit;
  }

  // Save to JSON file
  $db->add($w);
  $db->save();
  header('Location: /about/' . $w['name']);
  exit;
}

$tpl->render('new', [
  'title'      => 'Create a workspace'
]);