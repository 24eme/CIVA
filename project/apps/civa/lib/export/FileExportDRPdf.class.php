<?php

class FileExportDRPdf extends FileExportMiseADispo
{

    public function getAnnee() {

        return $this->getDocument()->campagne;
    }

    protected function getExport() {

        if (!$this->updateDocument()) {
            
            throw new sfException("Update failed : " . $this->getDocument()->_id);
        }

        return new ExportDRPdf($this->getDocument(), $this->_function_get_partial, 'pdf',  $this->_file_dir, false, $this->_filename);
    }

    protected function getFileDir() {

        return sfConfig::get('sf_data_dir') . '/export/dr/pdf/'.$this->getAnnee().'/';
    }

    protected function getFileName() {
        $doc = $this->getDocument();

        return sprintf("%s_%s_DR_%s_%s.pdf", $doc->campagne, $doc->declaration_insee, $doc->cvi, $doc->_rev);
    }

    protected function findDocument($id) {

        return acCouchdbManager::getClient()->find($id);
    }

    protected function updateDocument() {
        try {
            if (!$this->getDocument()->updated)
                throw new Exception();
        } catch (Exception $e) {
            try {
                $this->getDocument()->update();
                $this->getDocument()->save();
            } catch (Exception $exc) {

                return false;
            }
        }

        return true;
    }

}