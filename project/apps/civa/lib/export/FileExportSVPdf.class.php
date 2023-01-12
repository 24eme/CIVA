<?php

class FileExportSVPdf extends FileExportMiseADispo
{

    public function getAnnee() {

        return $this->getDocument()->getPeriode();
    }

    protected function getExport() {

        return new ExportSVPDF($this->getDocument(), 'pdf', $this->_file_dir, $this->_filename);
    }

    protected function getFileDir() {

        return sfConfig::get('sf_data_dir') . '/export/sv/pdf/'.$this->getAnnee().'/';
    }

    protected function getFileName() {

        return ExportSVPdf::buildFileName($this->getDocument(), false, true);
    }

    protected function findDocument($id) {

        return acCouchdbManager::getClient()->find($id);
    }

}
