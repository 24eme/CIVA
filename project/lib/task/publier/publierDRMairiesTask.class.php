<?php

class publierDRMairiesTask extends publierDRAbstractTask {

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
            new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'Publish for a campagne', '2010'),
            new sfCommandOption('htaccess', null, sfCommandOption::PARAMETER_REQUIRED, 'En cas de publication, force la recréation des htaccess', false),
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

        $this->cleanFile();

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

            if ($matches['annee'] != $options['campagne']) {
                continue;
            }

            // Répertoires
            $this->mkdirUnlessFolder($dr_publish_dir = $publish_dir . $matches['code_postal'] . '/');
            $generated_key = $this->generateKey($matches['code_postal']);
            $this->mkdirUnlessFolder($dr_publish_dir = $dr_publish_dir . $generated_key . '/');

            $web_path[$matches['code_postal']] = 'https://declaration.vinsalsace.pro/mairies/'.$matches['code_postal'].'/'.$generated_key.'/';

            // Htaccess
            if (!array_key_exists($dr_publish_dir, $htaccess)) {
                $this->createHtaccess($dr_publish_dir, $this->getHtaccessCommune(), $options['htaccess']);
                $htaccess[$dr_publish_dir] = true;
            }

            // Zip
            $filename_zip = $matches['code_postal'] . '_' . $matches['annee'] . '_DR.zip';
            if (!array_key_exists($filename_zip, $zips)) {
                $zips[$filename_zip] = new ZipArchive();
                $zips[$filename_zip]->open($dr_publish_dir . $filename_zip, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
            }

            // Clean Directory
            $this->mkdirUnlessFolder($dr_publish_dir = $dr_publish_dir . $matches['annee'] . '/');
            if (!array_key_exists($dr_publish_dir, $clear_directory)) {
                sfToolkit::clearDirectory($dr_publish_dir);
                $this->logSection('clear directory', $dr_publish_dir);
                $clear_directory[$dr_publish_dir] = true;
            }
            
            $publish_filename = $matches['code_postal'] . '-' . $matches['annee'] . '-DR-' . $matches['cvi'] . '.pdf';
            copy($file, $dr_publish_dir . $publish_filename);
            $zips[$filename_zip]->addFile($dr_publish_dir . $publish_filename, $publish_filename);

            $this->logSection('publier', $dr_publish_dir . $publish_filename);
            $nb_declaration++;
        }

        foreach ($zips as $filename_zip => $zip) {
            $zip->close();
            $this->logSection("zip created", $filename_zip);
        }

        $insee = $this->getInsee();
        foreach($web_path as $key => $item) {
            $this->logSection($key, $insee[$key] . ';' . $item);
        }

        $this->logSection('publier', $nb_declaration . ' declaration(s)');
    }

    protected function getInsee() {
        $insee = array();
        foreach (file(sfConfig::get('sf_data_dir') . '/import/Commune') as $c) {
	  $csv = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
	  $insee[$csv[0]] = $csv[1];
	}
        return $insee;
    }

    protected function getPublishDir() {
        $file_dir = sfConfig::get('sf_web_dir') . '/mairies/';
        $this->mkdirUnlessFolder($file_dir);
        return $file_dir;
    }

    protected function generateKey($code_postal) {
        return md5($code_postal.sfConfig::get('app_cle_secrete_publication_mairies'));
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

}
