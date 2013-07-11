<?php

class FileExportDSPdf
{
    protected $_file_dir = null;
    protected $_filename = null;
    protected $_ds = null;
    protected $_function_get_partial = null;

    public function __construct($id_ds, $function_get_partial) {
        $this->_function_get_partial = $function_get_partial;

        $this->_ds = acCouchdbManager::getClient()->find($id_ds, acCouchdbClient::HYDRATE_JSON);
        $this->_ds = DSCivaClient::getInstance()->getDSPrincipaleByDs($this->_ds, acCouchdbClient::HYDRATE_JSON);
        if (!$this->_ds) {

            throw new sfException("ds not found : " . $id_ds);
        }

        $this->_ds->annee = substr($this->_ds->periode, 0, 4);

        $this->_file_dir = sfConfig::get('sf_data_dir') . '/export/ds/pdf/'.$this->_ds->annee.'/';

        if (!is_dir($this->_file_dir)) {

            throw new sfException("path does not exist");
        }

        $dss = DSCivaClient::getInstance()->findDssByDS($this->_ds, acCouchdbClient::HYDRATE_JSON);

        $rev = null;
        foreach($dss as $ds) {
            $rev .= $ds->_rev;
        }
        $rev = md5($rev);
        $this->_filename = sprintf("%s_%s_DS_%s_%s.pdf", $this->_ds->periode, $this->_ds->declaration_insee, $this->_ds->declarant->cvi, $rev);

    }

    public function getDS() {
        
        return $this->_ds;
    }

    public function isExported() {

        return is_file($this->getPath());
    }

    public function export() {
        $ds = DSCivaClient::getInstance()->find($this->_ds->_id);

        if (!$ds) {

            throw new sfException("DS not found : " . $this->_ds->_id);
        }

        $document = new ExportDSPdf($ds, $this->_function_get_partial, 'pdf',  $this->_file_dir, false, $this->_filename);
        $document->generatePDF();

        return true;
    }

    public function getPath() {
        return $this->_file_dir . $this->_filename;
    }

}