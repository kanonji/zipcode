<?php 
class ZipcodesSchema extends CakeSchema {
    public $name = 'Zipcodes';

    public function before($event = array()) {
        return true;
    }

    public function after($event = array()) {
    }

    public $zipcodes = array(
        'id' => array('type' => 'string', 'length' => '7', 'null' => false, 'default' => NULL, 'key' => 'primary'),
        'prefecture' => array('type' => 'string', 'null' => false),
        'city' => array('type' => 'string', 'null' => false),
        'town' => array('type' => 'string', 'null' => true, 'default' => NULL),
        'block_number' => array('type' => 'string', 'null' => true, 'default' => NULL),
        'prefecture_ruby' => array('type' => 'string', 'null' => true, 'default' => NULL),
        'city_ruby' => array('type' => 'string', 'null' => true, 'default' => NULL),
        'town_ruby' => array('type' => 'string', 'null' => true, 'default' => NULL),
        'jiscode' => array('type' => 'string', 'length' => '5', 'null' => false),
        'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
        'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
        'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
    );
}