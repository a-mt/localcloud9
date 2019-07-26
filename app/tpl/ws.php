<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? $title : "Workspace" ?></title>
    <link rel="stylesheet" href="/css/loading-flat.css">
  </head>
  <body>
    <div id="loadingcontainer">
      <div id="loadingide">
          <div id="c9logo"></div>

          <div class="cool-message"></div>
          <div class="status"><div class="spinner"></div></div>

          <div id="content" class="loading-progress"></div>
          <div class="footer">
              <a href="https://docs.c9.io">Documentation</a> | 
              <a href="http://status.c9.io">Server Status</a> | 
          </div>
      </div>
    </div>
    <div class="msg"></div>