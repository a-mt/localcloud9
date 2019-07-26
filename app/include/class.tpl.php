<?php

class TPL {
    private static $instance;

    protected $tplpath;
    public $data = [];

    /**
     * Get singleton
     */
    public static function get() {
        if(!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Set the path to find the templates
     */
    private function __construct() {
        $this->tplpath = __DIR__ . '/../tpl/';
    }

    /**
     * Render a template
     * @param string $filename       - Name of the template, without .php extension. Ex: 'about'
     * @param array[optional] $opts  - List of variables to pass to the the template. Ex: ['title' => 'My title', 'workspaces' => []]
     * @param boolean[optional] $useLayout - [true]
     */
    public function render($filename, $opts = '', $useLayout = true) {
      extract($opts);

      // Check if there's an error set in session
      $err = false;
      if(isset($_SESSION['err'])) {
        $err = $_SESSION['err'];
        unset($_SESSION['err']);
      }

      if($useLayout === false) {
        require $this->tplpath . $filename . '.php';
        return;
      }

      // Get the content of the template
      ob_start();
      require $this->tplpath . $filename . '.php';
      $txt = ob_get_clean();

      // Render it in _layout
      require $this->tplpath . '_layout.php';
    }
}

$tpl = TPL::get();