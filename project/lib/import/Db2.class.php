<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Db2
 *
 * @author vince
 */
class Db2 {
    protected $_data = array();

    public function __construct($data) {
        $this->_data = $data;
    }

    public function getData() {

        return $this->_data;
    }

    public function get($column, $default = null) {
        if (!array_key_exists($column, $this->_data)) {
            print_r($this->_data);
            throw new sfException("DB2 Column ".$column." does not exist");
        }
        return ($this->_data[$column]) ? $this->_data[$column] : null;
    }

    public function set($column, $value) {

        $this->_data[$column] = $value;
    }
}
