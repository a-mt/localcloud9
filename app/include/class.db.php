<?php

class DB {
    private static $instance;

    protected $filepath;
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
     * Constructor
     * Get the list of data from JSON file
     */
    private function __construct() {
        $this->filepath = '/var/db/ws.json';

        if(!is_file($this->filepath)) {
            touch($this->filepath);
        }

        if($txt = @file_get_contents($this->filepath)) {
            $this->data = @json_decode($txt, true);
        }
    }

    /**
     * Check if there is an objet in the list with the given field and value
     * @param string $field
     * @param string $value
     * @return array | null
     */
    public function search($field, $value) {
        foreach($this->data as $i => $row) {
            if(isset($row[$field]) && $row[$field] === $value) {
                $row['index'] = $i;
                return $row;
            }
        }
    }

    /**
     * Add an objet to the list
     * @param array $row
     */
    public function add($row) {
        $this->data[] = $row;
    }

    /**
     * Update an objet of the list
     * @param array $row
     */
    public function update($row) {
        $index = $row['index'];
        unset($row['index']);

        $this->data[$index] = $row;
    }

    /**
     * Delete an objet of the list
     * @param array $row
     */
    public function delete($row) {
        unset($this->data[$row['index']]);
    }

    /**
     * Save the list to JSON file
     */
    public function save() {
      file_put_contents($this->filepath, json_encode($this->data));
    }
}

$db = DB::get();