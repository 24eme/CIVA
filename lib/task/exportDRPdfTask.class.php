<?php

class exportDRPdfTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
            new sfCommandOption('cvi', null, sfCommandOption::PARAMETER_REQUIRED, 'export pdf only for a given cvi (put "all" fo all cvi)', 'all'),
            new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'export cvi only for a campagne (put "all" fo all campagnes)', '2010'),
            new sfCommandOption('clean', null, sfCommandOption::PARAMETER_REQUIRED, 'Supprime les pdf ayant une révision obsolète', true),
            new sfCommandOption('publier', null, sfCommandOption::PARAMETER_REQUIRED, 'Publie les fichiers', false),
            new sfCommandOption('htaccess', null, sfCommandOption::PARAMETER_REQUIRED, 'En cas de publication, la recréation des htaccess', false),
        ));

        $this->namespace = 'export';
        $this->name = 'dr-pdf';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportDRPdf|INFO] task does things.
Call it with:

  [php symfony exportDRPdf|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '700M');

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        sfContext::createInstance($this->configuration);

        $dr_ids = array();

        if ($options['cvi'] == 'all' && $options['campagne'] == 'all') {
            $dr_ids = sfCouchdbManager::getClient("DR")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        } elseif ($options['cvi'] && $options['campagne'] == "all") {
            $dr_ids = array_keys(sfCouchdbManager::getClient("DR")->getArchivesCampagnes($options['cvi'], '2010'));
        } elseif ($options['cvi'] == 'all' && $options['campagne']) {
            $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        } elseif ($options['cvi'] && $options['campagne']) {
            $dr_ids = array(sfCouchdbManager::getClient("DR")->retrieveByCampagneAndCvi($options['cvi'], $options['campagne'])->_id);
        } else {
            new sfCommandException('invalid cvi and campagne');
        }
        
        foreach ($dr_ids as $id) {
            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id);
            try {
                if (!$dr->updated)
                    throw new Exception();
            } catch (Exception $e) {
                try {
                    $dr->update();
                    $dr->save();
                } catch (Exception $exc) {
                    $this->logSection("failed update", $dr->_id, null, "ERROR");
                    continue;
                }
            }
            
            try {
                $tiers = sfCouchdbManager::getClient("Tiers")->retrieveByCvi($dr->cvi);
                if (!$tiers) {
                    $this->logSection("unknow tiers", $dr->_id, null, "ERROR");
                    continue;
                }
                if ($dr->isValideeTiers()) {
                    $filename = $this->getDRFilename($dr, $tiers);
                    $document = new DocumentDR($dr, $tiers, array($this, 'getPartial'), 'pdf',  $this->getFileDir(), false, $filename);
                    $document->generatePDF();
                    $this->logSection($dr->_id, 'pdf generated ('. $this->getFileDir().$filename.')');
                    unset($document);
                }
            } catch (Exception $exc) {
                $this->logSection("failed pdf", $dr->_id, null, "ERROR");
                continue;
            }
            unset($dr);
            unset($tiers);
        }
        $this->logSection("export", "done");

        if ($options['clean']) {
            $this->cleanFile();
            $this->logSection("clean", "done");
        }

        if ($options['publier']) {
            if (!$options['clean']) {
                $this->cleanFile();
            }
            $this->publishFile($options);
            $this->logSection("publish", "done");
        }
    }

    protected function getFileDir() {
        $file_dir = sfConfig::get('sf_data_dir') . '/export/dr/pdf/';
        if (!file_exists($file_dir)) {
            mkdir(sfConfig::get('sf_data_dir') . '/export/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr/pdf/');
            $this->logSection($file_dir, 'folder created');
        }
        return $file_dir;
    }

    protected function getPublishDir() {
        $file_dir = sfConfig::get('sf_web_dir') . '/douane/';
        $this->mkdirUnlessFolder($file_dir);
        return $file_dir;
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
    
    protected function cleanFile() {
        $files = $this->getFiles();
        $drs_pdf = array();
        foreach($files as $file) {
            $filename = basename($file);
            preg_match($this->getRexexpFilename(true), $filename, $matches);
            $cvi = $matches['cvi'];
            $revision = $matches['revision'];
            $add = false;
            if (array_key_exists($cvi, $drs_pdf) && $drs_pdf[$cvi]['revision'] > $revision) {
                unlink($file);
                $this->logSection('deleted', $drs_pdf[$cvi]['path']);
                unset($drs_pdf[$cvi]);
            } elseif(array_key_exists($cvi, $drs_pdf) && $drs_pdf[$cvi]['revision'] < $revision) {
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
    }

    protected function publishFile($options) {
        $publish_dir = $this->getPublishDir();
        $this->createHtaccess($publish_dir, $this->getHtaccessGlobal(), $options['htaccess']);
        $files = $this->getFiles();
        $clear_directory = array();
        $htaccess = array();
        $zips = array();
        foreach($files as $file) {
            $filename = basename($file);
            preg_match($this->getRexexpFilename(true), $filename, $matches);
            $departement = substr($matches['code_postal'], 0, 2);
            $this->mkdirUnlessFolder($dr_publish_dir = $publish_dir . $departement . '/');
            if (!array_key_exists($dr_publish_dir, $htaccess)) {
                $this->createHtaccess($dr_publish_dir, $this->getHtaccessDepartement($departement), $options['htaccess']);
                $htaccess[$dr_publish_dir] = true;
            }
            $filename_zip = $departement.'_'.$matches['annee'].'_DR.zip';
            if (!array_key_exists($filename_zip, $zips)) {
                $zips[$filename_zip] = new ZipArchive();
                $zips[$filename_zip]->open($dr_publish_dir.$filename_zip, ZIPARCHIVE::OVERWRITE);
            }
            
            $this->mkdirUnlessFolder($dr_publish_dir = $dr_publish_dir . $matches['annee'] . '/');
            if (!array_key_exists($dr_publish_dir, $clear_directory)) {
                sfToolkit::clearDirectory($dr_publish_dir);
                $this->logSection('clear directory', $dr_publish_dir);
                $clear_directory[$dr_publish_dir] = true;
            }
            $publish_filename = $matches['code_postal'] . '-' . $matches['annee'] . '-DR-' . $matches['cvi'].'.pdf';
            copy($file, $dr_publish_dir.$publish_filename);
            $zips[$filename_zip]->addFile($dr_publish_dir.$publish_filename, $publish_filename);
            $this->logSection('publier', $dr_publish_dir .$publish_filename);
        }

        foreach($zips as $filename_zip => $zip) {
            $zip->close();
            $this->logSection("zip created", $filename_zip);
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

    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }

    public function createHtaccess($path, $content, $force = false) {
        $path = $path.'.htaccess';
        if (!file_exists($path) || $force) {
            file_put_contents($path, $content);
            $this->logSection('htaccess created', $path);
        }
    }

    protected function getHtaccessDepartement($dep) {
        return
sprintf(
"Options +Indexes
Allow from all
Require user viti%s",$dep
);
    }

    protected function getHtaccessGlobal() {
        return
sprintf("
Options -Indexes
Deny from all
AuthType Basic
AuthName \"Espace des professionnels du Vignoble d'Alsace\"
AuthUserFile %s
", sfConfig::get('sf_root_dir').'/htpasswd/viticulteurs');
    }

}
