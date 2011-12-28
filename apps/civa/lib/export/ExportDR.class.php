<?php

class ExportDR
{
    protected $_ids = null;
    protected $_file_dir = null;
    protected $_filename = null;
    protected $_export = null;
    protected $_debug = false;
    protected $_export_queue = array();

	public function __construct(Export $export, $function_get_partial, $debug = false) {
		$this->_export = $export;
		$this->_debug = $debug;
		$this->_ids = $this->findIds($export->drs);
        $this->_file_export_dr_pdfs = $this->generateFileExportDRPdfs($function_get_partial);
		$this->_file_dir = sfConfig::get('sf_web_dir') . '/mise_a_disposition/'.$this->_export->cle.'/declarations_de_recolte';
		$this->createFolder();
	}

	public function getIds() {
		return $this->_ids;
	}

    public function exportById($id) {
        $exported = false;

        if (!array_key_exists($id, $this->_file_export_dr_pdfs)) {
            throw new sfException("This dr not existing in this export : ".$id);
        }

        $file_export_dr = $this->_file_export_dr_pdfs[$id];
        
        if (!$file_export_dr->isExported()) {
            $file_export_dr->export();
            $exported = $file_export_dr->isExported();

            if ($exported && $this->_debug) {
                echo sprintf("-- export success : %s\n", $id);
            }
        }
        
        if (!$file_export_dr->isExported()) {
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
                    echo sprintf("-- export FAILED : %s\n", $id);
                }
            }
        }

        return $ids_exported;
    }

    public function export() {
        return $this->exportByIds($this->getIds());
    }

    public function publicationById($id) {

        if (!array_key_exists($id, $this->_file_export_dr_pdfs)) {
            throw new sfException("This dr not existing in this export : ".$id);
        }

        $file_export_dr = $this->_file_export_dr_pdfs[$id];

        $path = $this->_file_dir.'/'.$file_export_dr->getDrJson()->campagne.'/'.$file_export_dr->getDrJson()->_id.'.pdf';
        $this->mkdirUnlessFolder($this->_file_dir.'/'.$file_export_dr->getDrJson()->campagne);

        if (is_file($path)) {
            echo sprintf("(remove existing pdf %s)\n", $path);
            unlink($path);
        }

        copy($file_export_dr->getPath(), $path);

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
                    echo sprintf("-- publication FAILED : %s\n", $id);
                }
            }
           
        }
    }

    public function publication() {
        $this->publicationByIds($this->getIds());
    }

    public function zip() {
        $directories = sfFinder::type('directory')->relative()->in($this->_file_dir);
        foreach($directories as $directory) {
            $files = sfFinder::type('file')->in($this->_file_dir . '/' . $directory);
            if (count($files) > 0) {
                $zip_path = $this->_file_dir . '/' . $directory.'.zip';
                if (is_file($zip_path)) {
                    echo sprintf("(remove existing zip %s)\n", $zip_path);
                    unlink($zip_path);
                }
                $zip = new ZipArchive();
                $zip->open($zip_path, ZIPARCHIVE::OVERWRITE);
                foreach($files as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();

                if ($this->_debug) {
                    echo sprintf("zip created %s\n", $zip_path);
                }
            }
        }
    }

    public function clean() {
        sfToolkit::clearDirectory($this->_file_dir);
        if ($this->_debug) {
                echo sprintf("(clean folder %s)\n", $this->_file_dir);
            }
    }

    protected function generateFileExportDRPdfs($function_get_partial) {
        $file_export_dr_pdfs = array();
        foreach ($this->getIds() as $id) {
            try {
                $file_export_dr_pdfs[$id] = new FileExportDRPdf($id, $function_get_partial);
            } catch (Exception $e) {
                if ($this->_debug) {
                    echo sprintf("-- find FAILED : %s\n", $id);
                }
            }
        }
        return $file_export_dr_pdfs;
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

	protected function findIds($drs) {
		$ids = $drs->ids->toArray();

        foreach($drs->views as $view) {
            $ids = array_merge(
                    $ids, 
                    DRClient::getInstance()->startkey($view->startkey->toArray())
                                           ->endkey($view->endkey->toArray())
                                           ->executeView($view->id, $view->nom, sfCouchdbClient::HYDRATE_JSON)->getIds()
                   );
        }

		return $ids;
	}
}