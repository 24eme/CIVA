<?php

class ExportDR
{
    protected $_ids = null;
    protected $_file_dir = null;
    protected $_filename = null;
    protected $_export = null;
    protected $_debug = false;

	public function __construct(Export $export, $debug = false) {
		$this->_export = $export;
		$this->_debug = $debug;
		$this->_ids = $this->findIds($export->drs);
		$this->_file_dir = sfConfig::get('sf_web_dir') . '/mise_a_disposition/'.$this->_export->cle.'/declarations_de_recolte';
		$this->createFolder();
	}

	public function getIds() {

		return $this->_ids;
	}

	public function exportById($id, $function_get_partial) {
			if ($this->_debug) {
            	echo sprintf("%s\n", $id);
            }

			$file_export_dr = new FileExportDRPdf($id, $function_get_partial);
            
            if (!$file_export_dr->isExported()) {
                $file_export_dr->export();
            }
            
            if (!$file_export_dr->isExported()) {
            	throw new sfException("Export failed : ".$id);
        	}

        	$this->publication($file_export_dr);

        	if ($this->_debug) {
            	echo sprintf("-- export success\n\n", $id);
            }
    }

    public function export($function_get_partial) {
		foreach ($this->getIds() as $id) {
			try {
				$this->exportById($id, $function_get_partial);
			} catch (Exception $e) {
				if ($this->_debug) {
            		echo sprintf("-- export FAILED\n\n", $id);
            	}
			}
        }
    }

    public function createZip() {
        $directories = sfFinder::type('directory')->relative()->in($this->_file_dir);
        foreach($directories as $directory) {
            $files = sfFinder::type('file')->in($this->_file_dir . '/' . $directory);
            if (count($files) > 0) {
                $zip_path = $this->_file_dir . '/' . $directory.'.zip';
                $zip = new ZipArchive();
                $zip->open($zip_path, ZIPARCHIVE::OVERWRITE);
                foreach($files as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();

                if ($this->_debug) {
                    echo sprintf("zip created %s\n\n", $zip_path);
                }
            }
        }
    }

    protected function createFolder() {
    	$this->mkdirUnlessFolder(sfConfig::get('sf_web_dir') . '/mise_a_disposition');
    	$this->createHtaccess(sfConfig::get('sf_web_dir') . '/mise_a_disposition', $this->getHtaccessDeny());

    	$this->mkdirUnlessFolder(sfConfig::get('sf_web_dir') . '/mise_a_disposition/'.$this->_export->cle);
    	$this->createHtaccess(sfConfig::get('sf_web_dir') . '/mise_a_disposition/'.$this->_export->cle, $this->getHtaccessAllow());

    	$this->mkdirUnlessFolder($this->_file_dir);
    }

    public function cleanFolder() {
    	sfToolkit::clearDirectory($this->_file_dir);
        if ($this->_debug) {
                echo sprintf("(clean folder %s)\n\n", $this->_file_dir);
            }
    }

    protected function publication($file_export_dr) {
    	$path = $this->_file_dir.'/'.$file_export_dr->getDrJson()->campagne.'/'.$file_export_dr->getDrJson()->_id.'.pdf';
    	$this->mkdirUnlessFolder($this->_file_dir.'/'.$file_export_dr->getDrJson()->campagne);
    	copy($file_export_dr->getPath(), $path);
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