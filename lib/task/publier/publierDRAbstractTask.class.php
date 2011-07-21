<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of publierAbstractTask
 *
 * @author vince
 */
abstract class publierDRAbstractTask extends sfBaseTask {
    
    protected function getFileDir() {
        $file_dir = sfConfig::get('sf_data_dir') . '/export/dr/pdf/';
        return $file_dir;
    }

    protected function createFileDir() {
        if (!file_exists($this->getFileDir())) {
            mkdir(sfConfig::get('sf_data_dir') . '/export/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/pdf/');
            $this->logSection($file_dir, 'folder created');
        }
    }

    protected function getDRFilename($dr, $tiers) {
        return $dr->campagne.'_'.$dr->declaration_insee.'_DR_'.$tiers->cvi.'_'.$dr->_rev.'.pdf';
    }

    protected function getRexexpFilename($with_matches = false) {
        if ($with_matches) {
            return '/^(?P<annee>[0-9]{4})_(?P<code_postal>[0-9]{5})_DR_(?P<cvi>[0-9]{10})_(?P<revision>[0-9]+)-.+\.pdf/';
        } else {
            return '/^[0-9]{4}_[0-9]{5}_DR_[0-9]{10}_[0-9]+-.+\.pdf/';
        }
    }

    protected function getFiles() {
        return sfFinder::type('file')->name($this->getRexexpFilename())->in($this->getFileDir());
    }

    protected function mkdirUnlessFolder($path) {
        if (!file_exists($path)) {
            $resultat = mkdir($path);
            $this->logSection('folder created', $path);
            return true;
        }
        return true;
    }

    protected function createHtaccess($path, $content, $force = false) {
        $path = $path . '.htaccess';
        if (!file_exists($path) || $force) {
            file_put_contents($path, $content);
            $this->logSection('htaccess created', $path);
        }
    }

    protected function cleanFile() {
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
    }
}
?>
