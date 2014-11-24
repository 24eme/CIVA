<?php

/**
 * Description of ExportDRAcheteursCsv
 *
 * @author vince
 */
class ExportDRAcheteurCsv {

    protected $_acheteur = null;
    protected $_debug = false;
    protected $_has_dr = null;
    protected $_ids_dr = null;
    protected $_campagne = null;
    protected $_csv = null;

    /**
     *
     * @param string $campagne 
     */
    public function __construct($campagne, $acheteur_or_cvi, $debug = false) {
        $this->_debug = $debug;
        $this->_campagne = $campagne;
        if ($acheteur_or_cvi instanceof Acheteur) {
            $this->_acheteur = $acheteur_or_cvi;
        } else {
            $this->_acheteur = acCouchdbManager::getClient("Acheteur")->retrieveByCvi($acheteur_or_cvi);
        }
        if (!$this->_acheteur) {
            throw new sfException("Acheteur not find");
        }
        $drs = acCouchdbManager::getClient("DR")->findAllByCampagneAndCviAcheteur($this->_campagne, $this->_acheteur->cvi, acCouchdbClient::HYDRATE_JSON);

        $this->_ids_dr = $drs->getIds();
        $this->_has_dr = (count($this->_ids_dr) > 0);
        $this->csv = null;
    }
    
    public function hasDR() {
        return $this->_has_dr;
    }

    public function export() {
        $this->csv = implode(";", ExportDRCsv::$_headers)."\n";
        foreach ($this->_ids_dr as $id_dr) {
            preg_match("/^DR-([0-9]+)-([0-9]+)$/", $id_dr, $matches);

            $csvContruct = new ExportDRCsv($matches[2], $matches[1], false);         
            $csvContruct->export();
        
            $this->csv .= $csvContruct->output();
        }
        if ($this->_debug) {
            echo "------------ \n" . count($this->_ids_dr) . " DRs \n ------------\n";
        }
    }

    public function output() {

        return $this->csv;
    }
}