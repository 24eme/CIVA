<?php

abstract class ExportMiseADispo
{
    protected $_ids = null;
    protected $_file_dir = null;
    protected $_filename = null;
    protected $_export = null;
    protected $_debug = false;
    protected $_export_queue = array();
    protected $_hash_md5 = array();
    protected $_hash_md5_from_file = array();
    protected $_campagnes = null;
    protected $_file_export_document_pdf = null;

	public function __construct(Export $export, $function_get_partial, $debug = false) {
		$this->_export = $export;
		$this->_debug = $debug;
        $this->_ids = array();
        if($export->exist($this->getExportNodeName())) {
		  $this->_ids = $this->findIds($export->get($this->getExportNodeName()));
        }
        $this->_file_export_document_pdfs = $this->generateFileExportPdfs($function_get_partial);
		$this->_file_dir = sfConfig::get('sf_web_dir') . '/mise_a_disposition/'.$this->_export->cle.'/'.$this->getDocumentFolder();
		$this->createFolder();
	}

	public function getIds() {
		return $this->_ids;
	}

    public function exportById($id) {
        $exported = false;

        if (!array_key_exists($id, $this->_file_export_document_pdfs)) {
            throw new sfException("This ds not existing in this export : ".$id);
        }

        $file_export_document = $this->_file_export_document_pdfs[$id];
        
        if (!$file_export_document->isExported()) {
            $file_export_document->export();
            $exported = $file_export_document->isExported();

            if ($exported && $this->_debug) {
                echo sprintf("-- export success : %s\n", $id);
            }
        }
        
        if (!$file_export_document->isExported()) {
            throw new sfException("Export failed : ".$id);
        }

        return $exported; 
    }

    public function exportByIds($ids) {
        $ids_exported = array();

        foreach ($ids as $id) {
            try {
                if ($this->exportById($id)) {
                    $ids_exported[] = $id;            
                }
            } catch (Exception $e) {
                if ($this->_debug) {
                    echo sprintf("-- export FAILED : %s (%s)\n", $id, $e->getMessage());
                }
            }
        }

        return $ids_exported;
    }

    public function export() {
        return $this->exportByIds($this->getIds());
    }

    public function publicationById($id) {

        if(!preg_match("/^[A-Z0-9]+-(67|68)/", $id)) {
            throw new sfException("This document is a test document : ".$id);
        }

        if (!array_key_exists($id, $this->_file_export_document_pdfs)) {
            throw new sfException("This document not existing in this export : ".$id);
        }

        $file_export_document = $this->_file_export_document_pdfs[$id];
        $campagne = $file_export_document->getAnnee();

        if ($this->getHashMd5($campagne) && $this->getHashMd5($campagne) == $this->getHashMd5FileFromFile($campagne)) {
            return;
        }
        
        $path_campagne = $this->_file_dir.'/'.$file_export_document->getAnnee();
        $path = $path_campagne.'/'.$this->getFileName($file_export_document);
        $this->mkdirUnlessFolder($this->_file_dir.'/'.$file_export_document->getAnnee());
        $files = sfFinder::type("file")->name('/'.$this->getFileNameForMatch($file_export_document)."/")->in($path_campagne);
        foreach($files as $file) {
           echo sprintf("remove existing pdf %s\n", $file);
           unlink($file); 
        }

        if (!($file_export_document->getDocument()->validee && $file_export_document->getDocument()->modifiee) || ($file_export_document->getDocument()->exist('import_db2') && $file_export_document->getDocument()->import_db2)) {
            throw new sfException("This document in not valid or has been imported from db2 : ".$id);
        }

        link($file_export_document->getPath(), $path);

        if ($this->_debug) {
            echo sprintf("-- publication success: %s\n", $id);
        }
    }

    public function publicationByIds($ids) {
        foreach($ids as $id) {
            try {
                $this->publicationById($id);
            } catch (Exception $e) {
                if ($this->_debug) {
                    echo sprintf("-- publication FAILED : %s (%s)\n", $id, $e->getMessage());
                }
            }
           
        }
    }

    public function publication() {
        $this->publicationByIds($this->getIds());
    }

    public function zipByCampagne($campagne) {
        if ($this->getHashMd5($campagne) == $this->getHashMd5FileFromFile($campagne)) {
            return;
        }

        $files = sfFinder::type('file')->in($this->_file_dir . '/' . $campagne);
        if (count($files) > 0) {
            $zip_path = $this->_file_dir . '/' . $campagne.'.zip';
            if (is_file($zip_path)) {
                echo sprintf("remove existing zip %s\n", $zip_path);
                unlink($zip_path);
            }
            $zip = new ZipArchive();
            $zip->open($zip_path, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
            foreach($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();

            if ($this->_debug) {
                echo sprintf("zip created %s\n", $zip_path);
            }
        }
    }

    public function zip() {
        foreach($this->_campagnes as $campagne) {
            $this->zipByCampagne($campagne);
        }
    }

    public function getHashMd5($campagne) {
        if (!array_key_exists($campagne, $this->_hash_md5)) {
            $hash_md5 = '';
            foreach($this->_file_export_document_pdfs as $id => $file_export_document_pdf) {
                if ($file_export_document_pdf->getAnnee() == $campagne) {
                    $hash_md5 .=  $id.$file_export_document_pdf->getDocument()->_rev;
                }
            }
            $this->_hash_md5[$campagne] = md5($hash_md5);
        }

        return $this->_hash_md5[$campagne];
    }

    public function clean() {
        sfToolkit::clearDirectory($this->_file_dir);
        if ($this->_debug) {
                echo sprintf("(clean folder %s)\n", $this->_file_dir);
        }
    }

    public function createHashMd5File() {
        foreach($this->_campagnes as $campagne) {
            $this->createHashMd5FileByCampagne($campagne);
        }
    }

    public function createHashMd5FileByCampagne($campagne) {
        if ($this->getHashMd5($campagne) == $this->getHashMd5FileFromFile($campagne)) {
            return;
        }
        $path = $this->_file_dir.'/'.$campagne.'.checksum';
        if (is_file($path)) {
            echo sprintf("remove checksum file %s\n", $path);
            unlink($path);
        }

        if ($this->getHashMd5($campagne)) {
            file_put_contents($path, $this->getHashMd5($campagne));
            if ($this->_debug) {
                echo sprintf("-- create checksum file %s\n", $path);
            }   
        }

        if(array_key_exists($campagne, $this->_hash_md5_from_file)) {
            unset($this->_hash_md5_from_file[$campagne]);
        }
    }

    protected function generateFileExportPdfs($function_get_partial) {
        $file_export_document_pdfs = array();
        foreach ($this->getIds() as $id) {
            try {
                $file_explort_class_name = $this->getFileExportClassName();
                $file_export_document_pdfs[$id] = new $file_explort_class_name($id, $function_get_partial);
                if (!in_array($file_export_document_pdfs[$id]->getAnnee(), $this->_campagnes)) {
                    $this->_campagnes[] = $file_export_document_pdfs[$id]->getAnnee();
                }
            } catch (Exception $e) {
                if ($this->_debug) {
                    echo sprintf("-- find FAILED : %s (%s)\n", $id, $e->getMessage());
                }
            }
            }
    
        return $file_export_document_pdfs;
    }

    protected function getHashMd5FileFromFile($campagne) {

        if (!array_key_exists($campagne, $this->_hash_md5_from_file)) {
            $path = $this->_file_dir.'/'.$campagne.'.checksum';

            $this->_hash_md5_from_file[$campagne] = null;

            if (is_file($path)) {
               $this->_hash_md5_from_file[$campagne] = file_get_contents($path);
            }
        }

        return $this->_hash_md5_from_file[$campagne];
    }

    protected function createFolder() {
    	$this->mkdirUnlessFolder(sfConfig::get('sf_web_dir') . '/mise_a_disposition');
    	$this->createHtaccess(sfConfig::get('sf_web_dir') . '/mise_a_disposition', $this->getHtaccessDeny());

    	$this->mkdirUnlessFolder(sfConfig::get('sf_web_dir') . '/mise_a_disposition/'.$this->_export->cle);
    	$this->createHtaccess(sfConfig::get('sf_web_dir') . '/mise_a_disposition/'.$this->_export->cle, $this->getHtaccessAllow());

    	$this->mkdirUnlessFolder($this->_file_dir);
    }

    protected function createHtaccess($path, $content, $force = false) {
        $path = $path . '/.htaccess';
        if (!file_exists($path) || $force) {
            file_put_contents($path, $content);
            if ($this->_debug) {
            	echo sprintf("(htaccss created %s)\n", $path);
            }
        }
    }

    protected function getHtaccessDeny() {

        return sprintf(
                "Options -Indexes\nDeny from all");
    }

    protected function getHtaccessAllow() {

        return sprintf(
                "Options +Indexes\nAllow from all");
    }

    protected function mkdirUnlessFolder($path) {
        if (!file_exists($path)) {
            $resultat = mkdir($path);
            if ($this->_debug) {
            	echo sprintf("(folder created %s)\n", $path);
            }

            return true;
        }

        return true;
    }

	protected function findIds($node) {
		$ids = $node->ids->toArray();

        foreach($node->views as $view) {
            $startkey = $this->convertKey($view->startkey->toArray());
            $endkey = $this->convertKey($view->endkey->toArray());

            $ids = array_merge(
                    $ids, 
                    acCouchdbManager::getClient()->startkey($startkey)
                                           ->endkey($endkey)
                                           ->executeView($view->id, $view->nom, acCouchdbClient::HYDRATE_JSON)->getIds()
                   );
        }

		return $ids;
	}

    protected function convertKey($keys) {
        foreach($keys as $key => $value) {
            if($value == "[]") {
                $keys[$key] = array();
            }
        }

        return $keys;
    }

    abstract protected function getExportNodeName();

    abstract protected function getDocumentFolder();

    abstract protected function getFileExportClassName();

    abstract protected function getFileName($file_export);

}