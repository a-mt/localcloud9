<main>
  <header class="flex-center-justify workspace-page-header grey padding border-bottom">
    <h1 class="h1"><a href="/" class="lighten">Workspaces</a>/<a href="/about/<?= $name ?>"><?= $name ?></a></h1>
    <div class="flex-center-justify">
      <a class="button solid" href="/ws/<?= $name ?>">Open</a>
    </div>
  </header>
  <section class="workspace-page-section padding">

    <form class="border-bottom" method="POST">
      <div>
        <strong>Description</strong>
        <textarea rows="4" name="description" placeholder="Add a short description of your workspace"><?= $w['description'] ?></textarea>
      </div>
      <br>
      <div>
        <strong>Workspace Auth</strong>
        <p class="no-margin-top">You can protect your workspace with a password.<br> You will have to enter the username and password of your choice when trying to access it.</p>
        <input name="auth" type="text" value="<?= $w['auth'] ?>" disabled>
      </div>
      <!--<div>
        <strong>Workspace Visibility</strong>
        <p class="no-margin-top">You can change the visibilty of your workspace. Public workspaces will be listed under your public profile, and can be viewed by other Cloud9 users.</p>
        <div class="flex-center-justify">
          <label class="flex-top-left">
            <input type="radio" name="visible" value="0"<?= ($w['visible'] ? '' : ' checked') ?>>
            <div>
              <strong>Private</strong><br>
              <small class="lighten">This is a workspace for your eyes only</small>
            </div>
          </label>
          <label class="flex-top-left">
            <input type="radio" name="visible" value="1"<?= ($w['visible'] ? ' checked' : '') ?>>
            <div>
              <strong>Public</strong><br>
              <small class="lighten">This will create a workspace for everybody to see</small>
            </div>
          </label>
        </div>-->
        <br>

      </div>
      <p><button class="button success" type="submit" name="submit">Save</button></p>
    </form>

    <br>
    <div class="card danger">
      <header>
        <h4>Danger zone</h4>
      </header>
      <section class="flex-center-justify">
        <div>
          <h4 class="no-margin-bottom">Delete this workspace</h4>
          <p class="no-margin-top">Deleting a workspace can't be undone.<br> Please make sure you're removing the correct one!</p>
        </div>
        <button id="button-delete-workspace" class="button danger fat">Delete workspace</button>
      </section>
    </div>

    <div id="delete-workspace" class="modal" style="display: none">
      <form class="card fadeIn" method="POST">
        <header>
          <i class="close float-right">&times;</i>
          <h4>Delete <?= $name ?></h4>
        </header>
        <section>
          <p class="no-margin-top">Please confirm that you would like to delete this workspace by entering <strong><?= $name ?></strong> into the textfield below</p>
          <input type="text" autocomplete="off" data-confirm="<?= $name ?>">
        </section>
        <footer class="text-right">
          <button class="button danger" disabled type="submit" name="delete">Confirm</button>
          <button class="button solid close" type="button">Cancel</button>
        </footer>
      </form>
    </div>

  </section>
</main>

<script>
  (function(){
    var deleteWsBtn   = document.getElementById('button-delete-workspace'),
        deleteWsModal = document.getElementById('delete-workspace'),
        input         = deleteWsModal.querySelector('input'),
        closeBtn      = deleteWsModal.querySelectorAll('.close'),
        confirmBtn    = deleteWsModal.querySelector('.danger'),
        timer         = null,
        card          = deleteWsModal.firstElementChild;

      // Close modal when clicking on modal background
      deleteWsModal.addEventListener('click', function(){
        deleteWsModal.style.display = 'none';
      });
      card.addEventListener('click', function(e){
        e.stopPropagation();
      });
      closeBtn.forEach((btn) => { btn.addEventListener('click', function(){
          deleteWsModal.style.display = 'none';
        })
      });

      // Enable submit when text.value == name
      var enableSubmit = function() {
        if(input.value === input.getAttribute('data-confirm')) {
          confirmBtn.removeAttribute('disabled');
        } else {
          confirmBtn.setAttribute('disabled', true);
        }
      }
      input.addEventListener('keyup', function(){
        clearTimeout(timer);
        timer = setTimeout(enableSubmit, 300);
      });

      // Open modal when clicking on "Delete workspace"
      deleteWsBtn.addEventListener('click', function(){
        input.value = '';
        confirmBtn.setAttribute('disabled', true);

        deleteWsModal.style.display = 'block';
      });
  })();
</script>