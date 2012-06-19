<?php

class FileExportDRPdf
{
    protected $_file_dir = null;
    protected $_filename = null;
    protected $_dr_json = null;
    protected $_function_get_partial = null;

    public function __construct($id_dr, $function_get_partial) {
        $this->_function_get_partial = $function_get_partial;

        $this->_dr_json = sfCouchdbManager::getClient()->retrieveDocumentById($id_dr, sfCouchdbClient::HYDRATE_JSON);
        if (!$this->_dr_json) {

            throw new sfException("dr not found : " . $id_dr);
        }

        $this->_file_dir = sfConfig::get('sf_data_dir') . '/export/dr/pdf/'.$this->_dr_json->campagne.'/';

        if (!is_dir($this->_file_dir)) {

            throw new sfException("path does not exist");
        }

        $this->_filename = $this->_dr_json->campagne.'_'.$this->_dr_json->declaration_insee.'_DR_'.$this->_dr_json->cvi.'_'.$this->_dr_json->_rev.'.pdf';
    }

    public function getDrJson() {
        
        return $this->_dr_json;
    }

    public function isExported() {

        return is_file($this->getPath());
    }

    public function export() {
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($this->_dr_json->_id);

        if (!$dr) {

            throw new sfException("DR not found : " . $this->_dr_json->_id);
        }

        $recoltant = $dr->getRecoltantObject();

        if (!$recoltant) {
            
            throw new sfException("Recoltant failed : " . $this->_dr_json->_id);  
        }

        if (!$this->updateDR($dr)) {
            
            throw new sfException("Update failed : " . $this->_dr_json->_id);
        }

        $document = new ExportDRPdf($dr, $recoltant, $this->_function_get_partial, 'pdf',  $this->_file_dir, false, $this->_filename);
        $document->generatePDF();

        return true;
    }

    public function getPath() {
        return $this->_file_dir . $this->_filename;
    }

    protected function updateDR($dr) {
        try {
            if (!$dr->updated)
                throw new Exception();
        } catch (Exception $e) {
            try {
                $dr->update();
                $dr->save();
            } catch (Exception $exc) {

                return false;
            }
        }

        return true;
    }

    /*protected function getRexexpFilename($with_matches = false) {
        if ($with_matches) {
            return '/^(?P<annee>[0-9]{4})_(?P<code_postal>[0-9]{5})_DR_(?P<cvi>[0-9]{10})_(?P<revision>[0-9]+)-.+\.pdf/';
        } else {
            return '/^[0-9]{4}_[0-9]{5}_DR_[0-9]{10}_[0-9]+-.+\.pdf/';
        }
    }*/

    /*protected function getFileDir() {

        return $this->_file_dir;
    }*/

    /**protected function createFileDir() {
        if (!file_exists($this->_file_dir) {
            mkdir(sfConfig::get('sf_data_dir') . '/export/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/pdf/');
            $this->logSection($this->_file_dir, 'folder created');
        }
    }**/

    /*protected function getDRFilename($dr, $tiers) {
        
        return $this->_filename;
    }*/

    /*protected function getFiles() {
        return sfFinder::type('file')->name($this->getRexexpFilename())->in($this->_file_dir);
    }*/

    /*protected function mkdirUnlessFolder($path) {
        if (!file_exists($path)) {
            $resultat = mkdir($path);
            $this->logSection('folder created', $path);
            return true;
        }
        return true;
    }*/

    /*protected function cleanFile() {
        $nb_clean = 0;
        $files = $this->getFiles();
        $drs_pdf = array();
        foreach($files as $file) {
            $filename = basename($file);
            preg_match($this->getRexexpFilename(true), $filename, $matches);
            $cvi = $matches['cvi'];
            $revision = $matches['revision'];
            $add = false;
            if (array_key_exists($cvi, $drs_pdf) && $drs_pdf[$cvi]['revision'] > $revision) {
                $nb_clean++;
                unlink($file);
                $this->logSection('deleted', $drs_pdf[$cvi]['path']);
                unset($drs_pdf[$cvi]);
            } elseif(array_key_exists($cvi, $drs_pdf) && $drs_pdf[$cvi]['revision'] < $revision) {
                $nb_clean++;
                unlink($drs_pdf[$cvi]['path']);
                $this->logSection('deleted', $drs_pdf[$cvi]['path']);
                unset($drs_pdf[$cvi]);
                $add = true;
            } else {
                $add = true;
            }

            if ($add) {
                $drs_pdf[$cvi] = array('revision' => $revision, 'path' => $file);
            }
        }

        $this->logSection("clean", $nb_clean.' file(s)');
    }*/

}