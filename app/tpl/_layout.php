<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?= $title ? $title : "Workspaces" ?></title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body<?php if(isset($theme)) echo ' class="' . $theme . '"'; ?>>
    <?= $txt ?>
  </body>
</html>