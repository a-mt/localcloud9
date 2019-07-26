<header class="flex-center-justify workspace-page-header grey padding border-bottom">
  <h1 class="h1"><a href="/" class="lighten">Workspaces</a>: Create a new workspace</h1>
</header>

<main>
  <form class="workspace-page-section padding" method="POST">
    <?php if($err) {
      echo '<div class="alert danger">' . $err . '</div>';
    } ?>

    <div>
      <strong>Workspace name</strong>
      <input name="name" type="text" placeholder="your-project-name" required maxlength="255" pattern="[A-Za-z0-9-]{1,255}" title="Alphanumerics and dashes">
    </div>
    <br>

    <div>
      <strong>Description</strong>
      <input name="description" type="text" placeholder="Add a short description of your workspace">
    </div>
    <br>

    <div>
      <strong>Template</strong>
      <fieldset>
        <input name="template" type="radio" value="" id="default" checked>
        <label for="default">Python + Node</label>

        <input name="template" type="radio" value="php" id="php">
        <label for="php">PHP</label>
      </fieldset>
    </div>
    <br>

    <div>
      <strong>Workspace Auth</strong>
      <p class="no-margin-top">You can protect your workspace with a password.<br> You will have to enter the username and password of your choice when trying to access it.</p>
      <input name="auth" type="text" placeholder="username:password">
    </div>

    <!--<div>
      <strong>Workspace Visibility</strong>
      <p class="no-margin-top">You can change the visibilty of your workspace. Public workspaces will be listed under your public profile, and can be viewed by other Cloud9 users.</p>
      <div class="flex-center-justify">
        <label class="flex-top-left">
          <input type="radio" name="visible" value="0">
          <div>
            <strong>Private</strong><br>
            <small class="lighten">This is a workspace for your eyes only</small>
          </div>
        </label>
        <label class="flex-top-left">
          <input type="radio" name="visible" value="1" checked="">
          <div>
            <strong>Public</strong><br>
            <small class="lighten">This will create a workspace for everybody to see</small>
          </div>
        </label>
      </div>
    </div>-->
    <br>

    <p><button class="button success fat" type="submit" name="submit">Create workspace</button></p>
  </form>
</main>