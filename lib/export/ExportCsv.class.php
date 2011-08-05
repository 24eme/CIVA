<?php

/**
 * Description of ExportCsv
 *
 * @author vince
 */
class ExportCsv {

    /**
     *
     * @var string
     */
    protected $_content = null;

    public function __construct($headers = null) {
        if ($headers) {
            $this->add($headers);
        }
    }

    /**
     *
     * @param array $data
     * @param array $validation
     * @return string 
     */
    protected function implode($data, $validation) {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $validation)) {
                $value = $this->cast($value, $validation[$key]['type']);
                $this->validate($key, $value, $validation[$key]);
                $data[$key] = $this->filter($value, $validation[$key]);
            }
        }

        return implode(';', $data) . "\n";
    }

    protected function validate($key, $value, $options) {
        $type = $options['type'];
        $fn_type = "is_" . $type;
        /*if (!is_null($value) && !($fn_type($value))) {
            throw new sfException("not " . $type . " : " . $value . " (" . $key . ")");
        }*/
        if ($options['required'] && (!array_key_exists("default", $options) || $options['default'] === false)) {
            if ($value === null) {
                throw new sfException("required : ".$value." (".$key.")");
            }
        }
        return $value;
    }
    
    protected function cast($value, $type) {
        /*if ($value === null) {
           return $value;
        }
        if ($type == "string") {
           return (string) $value; 
        }
        if ($type == "int") {
           return (int) $value; 
        }
        if ($type == "float") {
           return (float) $value; 
        }*/
        return $value;
    }

    protected function filter($value, $options) {
        $type = $options['type'];
        if ($type == "string") {
            $value = '"' . str_replace('"', '\"', $value) . '"';
        }
        if ($type == "float" && ($value !== null || (array_key_exists("default", $options) && $options['default']))) {
            $value = sprintf($options['format'], $value);
        }
        return $value;
    }

    /**
     *
     * @param array $data 
     * @param array $validation
     * @return string
     */
    public function add($data, $validation = array()) {
        $line = $this->implode($data, $validation);
        $this->_content .= $line;
        return $line;
    }

    /**
     *
     * @return string 
     */
    public function output() {
        return $this->_content;
    }

}
