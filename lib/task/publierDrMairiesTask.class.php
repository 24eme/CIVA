<?php

class publierDrMairiesTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
            new sfCommandOption('htaccess', null, sfCommandOption::PARAMETER_REQUIRED, 'En cas de publication, la recréation des htaccess', false),
        ));

        $this->namespace = 'publier';
        $this->name = 'dr-mairies';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [publier:dr-mairies|INFO] task does things.
Call it with:

  [php symfony publier:dr-mairies|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $publish_dir = $this->getPublishDir();
        $this->createHtaccess($publish_dir, $this->getHtaccessGlobal(), $options['htaccess']);
        $files = $this->getFiles();
        $clear_directory = array();
        $htaccess = array();
        $web_path = array();
        $zips = array();
        $nb_declaration = 0;
        foreach ($files as $file) {
            $filename = basename($file);
            preg_match($this->getRexexpFilename(true), $filename, $matches);
            $this->mkdirUnlessFolder($dr_publish_dir = $publish_dir . $matches['code_postal'] . '/');
            $generated_key = $this->generateKey($matches['code_postal']);
            $this->mkdirUnlessFolder($dr_publish_dir = $dr_publish_dir . $generated_key . '/');
            $web_path[$matches['code_postal']] = 'http://declarations.vinsalsace.pro/mairies/'.$matches['code_postal'].'/'.$generated_key.'/';
            if (!array_key_exists($dr_publish_dir, $htaccess)) {
                $this->createHtaccess($dr_publish_dir, $this->getHtaccessCommune(), $options['htaccess']);
                $htaccess[$dr_publish_dir] = true;
            }
            $filename_zip = $matches['code_postal'] . '_' . $matches['annee'] . '_DR.zip';
            if (!array_key_exists($filename_zip, $zips)) {
                $zips[$filename_zip] = new ZipArchive();
                $zips[$filename_zip]->open($dr_publish_dir . $filename_zip, ZIPARCHIVE::OVERWRITE);
            }

            $this->mkdirUnlessFolder($dr_publish_dir = $dr_publish_dir . $matches['annee'] . '/');
            if (!array_key_exists($dr_publish_dir, $clear_directory)) {
                sfToolkit::clearDirectory($dr_publish_dir);
                $this->logSection('clear directory', $dr_publish_dir);
                $clear_directory[$dr_publish_dir] = true;
            }
            $publish_filename = $matches['code_postal'] . '-' . $matches['annee'] . '-DR-' . $matches['cvi'] . '.pdf';
            copy($file, $dr_publish_dir . $publish_filename);
            $nb_declaration++;
            $zips[$filename_zip]->addFile($dr_publish_dir . $publish_filename, $publish_filename);
            $this->logSection('publier', $dr_publish_dir . $publish_filename);
        }

        foreach ($zips as $filename_zip => $zip) {
            $zip->close();
            $this->logSection("zip created", $filename_zip);
        }

        foreach($web_path as $key => $item) {
            $this->logSection($key, $item);
        }

        $this->logSection('publier', $nb_declaration);

        // add your code here
    }

    protected function getPublishDir() {
        $file_dir = sfConfig::get('sf_web_dir') . '/mairies/';
        $this->mkdirUnlessFolder($file_dir);
        return $file_dir;
    }

    protected function getFileDir() {
        $file_dir = sfConfig::get('sf_data_dir') . '/export/dr/pdf/';
        return $file_dir;
    }

    protected function mkdirUnlessFolder($path) {
        if (!file_exists($path)) {
            $resultat = mkdir($path);
            $this->logSection('folder created', $path);
            return true;
        }
        return true;
    }

    protected function generateKey($code_postal) {
        return md5($code_postal.'MAIRIE-PASSWD');
    }

    protected function getFiles() {
        return sfFinder::type('file')->name($this->getRexexpFilename())->in($this->getFileDir());
    }

    protected function getRexexpFilename($with_matches = false) {
        if ($with_matches) {
            return '/^(?P<annee>[0-9]{4})_(?P<code_postal>[0-9]{5})_DR_(?P<cvi>[0-9]{10})_(?P<revision>[0-9]+)-.+\.pdf/';
        } else {
            return '/^[0-9]{4}_[0-9]{5}_DR_[0-9]{10}_[0-9]+-.+\.pdf/';
        }
    }

    protected function getHtaccessGlobal() {
        return sprintf(
                "Options -Indexes
Deny from all");
    }

    protected function getHtaccessCommune() {
        return sprintf(
                "Options +Indexes
Allow from all");
    }

    protected function createHtaccess($path, $content, $force = false) {
        $path = $path . '.htaccess';
        if (!file_exists($path) || $force) {
            file_put_contents($path, $content);
            $this->logSection('htaccess created', $path);
        }
    }

}
