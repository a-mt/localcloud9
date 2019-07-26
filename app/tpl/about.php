<?php
  $status = 'Archived';
  $size   = '0B';

  if(isset($w['container'])) {
    $info = $w['container'];

    $status = ucfirst($info['State']) . ', ' . $info['Status'];
    $size   = $info['Size'] . ' (virtual ' . $info['SizeRoot'] . ')';
  }
?>
<main>
  <header class="flex-center-justify workspace-page-header grey padding border-bottom">
    <h1 class="h1"><a href="/" class="lighten">Workspaces</a>/<?= $name ?></h1>
    <div class="flex-center-justify">
      <a class="icon-cog" href="/settings/<?= $name ?>"></a>
      <a class="button solid" href="/ws/<?= $name ?>">Open</a>
    </div>
  </header>
  <section class="workspace-page-section padding">
    <h2 class="h2">Workspace info</h2>
    <h3 class="h3"><?= $status ?></h3>
    <p class="no-margin-bottom"><?= $w['description'] ? $w['description'] : "This workspace has no description yet." ?></p>

    <br>
    <h2 class="h2">Settings</h2>
    <div>
      <strong>CPU</strong>
      <span>1</span>
    </div>
    <div>
      <strong>RAM</strong>
      <span>2G</span>
    </div>

    <br>
    <h2 class="h2">Usage</h2>
    <div>
      <strong>Disk</strong>
      <span><?= $size ?></span>
    </div>
<!-- 
    <br>
    <h2 class="h2 border-bottom"><span class="link">README.md</span></h2>

<article class="markdown"><h1 id="book-list">Book list</h1>
<p>Features:</p>
<ul>
<li>List the books you own</li>
<li>Mark as read</li>
<li>Create a wishlist</li>
</ul>
<p>Use cases:</p>
<ul>
<li>As an authenticated user, you can<ul>
<li>View the books you own</li>
<li>Add a book you own, using Google Books API</li>
<li>Add a book you wish to have</li>
<li>Mark as read</li>
</ul>
</li>
</ul>
<h2 id="install">Install</h2>
<ol>
<li>Create a new <a rel="nofollow" href="https://console.developers.google.com/projectselector/apis/credentials" title="https://console.developers.google.com/projectselector/apis/credentials">Google project</a>,
generate an API key, and activate Google Books API for this project</li>
<li>Create a <a rel="nofollow" href="http://mlab.com/" title="http://mlab.com/">MongoDB database</a>, and create an user for this database</li>
<li>Copy <code>.env.example</code> to <code>.env</code> and update the variables in it</li>
<li><p>Install the dependencies</p>
<pre><code>npm install
</code></pre></li>
</ol>
<h2 id="run">Run</h2>
<pre><code>npm start
</code></pre><h2 id="dev-notes">Dev notes</h2>
<h3 id="import-thumbnails">Import thumbnails</h3>
<ul>
<li><p>Create an app</p>
<ul>
<li>Activate Google Drive API</li>
<li>Create Credentials: OAuth client ID</li>
<li>In OAuth consent screen, add the current host</li>
<li>In the details of the created client ID,
add <a rel="nofollow" href="https://yourapp:8081/oauth2callback" title="https://yourapp:8081/oauth2callback">https://yourapp:8081/oauth2callback</a> to the Authorized redirect URIs</li>
<li>Update the .env file</li>
</ul>
</li>
<li><p>Create a folder in Google Drive</p>
<ul>
<li>Get the sharing link of the folder (to make the folder public)</li>
<li>Copy the ID of the folder</li>
<li>Update the .env file</li>
</ul>
</li>
<li><p>Start webserver via node importThumbnails.js</p>
</li>
<li>Go to <a rel="nofollow" href="https://yourapp:8081" title="https://yourapp:8081">https://yourapp:8081</a></li>
</ul>
<h3 id="update-node">Update node</h3>
<pre><code>nvm install stable
npm i npm-check-updates
ncu -u
npm install
</code></pre></article>-->

  </section>
</main>