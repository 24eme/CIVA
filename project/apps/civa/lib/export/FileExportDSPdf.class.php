<?php

class FileExportDSPdf extends FileExportMiseADispo
{

    public function getAnnee() {

        return $this->getDocument()->getAnnee();
    }

    protected function getExport() {

        return new ExportDSPDF($this->getDocument(), $this->_function_get_partial, 'pdf',  $this->_file_dir, false, $this->_filename);
    }

    protected function getFileDir() {

        return sfConfig::get('sf_data_dir') . '/export/ds/pdf/'.$this->getAnnee().'/';
    }

    protected function getFileName() {
        $doc = $this->getDocument();

        return sprintf("%s_%s_DS_%s_%s.pdf", $doc->periode, $doc->declaration_insee, $doc->declarant->cvi, $this->getMD5File());
    }

    protected function getMD5File() {
        $dss = DSCivaClient::getInstance()->findDssByDS($this->getDocument(), acCouchdbClient::HYDRATE_JSON);

        $rev = null;
        foreach($dss as $ds) {
            $rev .= $ds->_rev;
        }

        return md5($rev);
    }

    protected function findDocument($id) {
        $document = acCouchdbManager::getClient()->find($id, acCouchdbClient::HYDRATE_JSON);
        $document = DSCivaClient::getInstance()->getDSPrincipaleByDs($document);
        
        return $document;
    }

}