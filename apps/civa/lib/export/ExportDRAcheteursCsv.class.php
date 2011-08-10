<?php

/**
 * Description of ExportDRAcheteursCsv
 *
 * @author vince
 */
class ExportDRAcheteursCsv extends ExportDRAcheteurCsv {

    protected $_exports = array();
    
    public function __construct($campagne, $debug = false) {
        $this->_debug = $debug;
        $drs = sfCouchdbManager::getClient("DR")->findAllByCampagneAcheteurs($campagne, sfCouchdbClient::HYDRATE_ON_DEMAND_WITH_DATA);
        $this->load($drs, $campagne, null);
    }

    public function add($datas, $validation = array()) {
        $cvi = $datas['cvi_acheteur'];
        if (!array_key_exists($cvi, $this->_exports)) {
            $this->_exports[$cvi] = new ExportCsv($this->_headers);
        }
        $line = $this->_exports[$cvi]->add($datas, $validation);
        if ($this->_debug) {
            echo $line;
        }
        return $line;
    }
   
    public function output() {
        $outputs = array();
        foreach($this->_exports as $cvi => $export) {
            $outputs[$cvi] = $export->output();
        }
        return $outputs;
    }

}