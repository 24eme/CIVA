<?php

class FileExportDSPdf extends FileExportMiseADispo
{

    public function getAnnee() {

        return $this->getDocument()->getAnnee();
    }

    protected function getExport() {

        return new ExportDSPDF($this->getDocument(), $this->_function_get_partial, false, 'pdf',  $this->_file_dir, false, $this->_filename);
    }

    protected function getFileDir() {

        return sfConfig::get('sf_data_dir') . '/export/ds/pdf/'.$this->getAnnee().'/';
    }

    protected function getFileName() {

        return ExportDSPdf::buildFileName($this->getDocument(), false, true);
    }

    protected function findDocument($id) {
        $document = acCouchdbManager::getClient()->find($id, acCouchdbClient::HYDRATE_JSON);
        $document = DSCivaClient::getInstance()->getDSPrincipaleByDs($document);
        
        return $document;
    }

}