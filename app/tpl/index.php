<h1 class="h1">Workspaces</h1>

<main class="row">
  <article class="col card card-new-project">
     <a class="flex-center-center" href="/new">
       <button class="icon-plus"></button>
       <strong>Create a new workspace</strong>
     </a>
  </article>

  <?php foreach($workspaces as $w) {
    $status = 'Archived';
    $size   = '0B';

    if(isset($containers[$w['name']])) {
      $status = ucfirst($containers[$w['name']]['State']) . ', ' . $containers[$w['name']]['Status'];
      $size   = $containers[$w['name']]['Size'];
    }
    ?>
    <article class="col card flex-justify">
      <section class="flex-justify">
        <header>
          <h2 class="h2"><?= $w['name'] ?></h2>
          <h3 class="h3"><?= $status ?></h3>
          <p><?= $w['description'] ?></p>
        </header>
        <div class="text-right">
          <a class="button success margin-right" href="/about/<?= $w['name'] ?>">About</a>
          <a class="button solid" href="/ws/<?= $w['name'] ?>">Open</a>
        </div>
      </section>
      <footer class="flex-center-justify">
        <span><strong>1</strong> CPU</span>
        <span><strong>2G</strong> RAM</span>
        <span><strong><?= $size ?></strong> HDD</span>
      </footer>
    </article>
  <?php } ?>
</main>