<?php

class publierDRViticulteursTask extends publierDRAbstractTask {

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
        $this->name = 'dr-viticulteurs';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [publier:dr-viticulteurs|INFO] task does things.
Call it with:

  [php symfony publier:dr-viticulteurs|INFO]
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
        $zips = array();
        $nb_declaration = 0;
        foreach ($files as $file) {
            $filename = basename($file);
            preg_match($this->getRexexpFilename(true), $filename, $matches);

            if ($matches['annee'] != $options['campagne']) {
                continue;
            }

            $departement = substr($matches['code_postal'], 0, 2);

            // Répertoires
            $this->mkdirUnlessFolder($dr_publish_dir = $publish_dir . $departement . '/');

            // Htaccess
            if (!array_key_exists($dr_publish_dir, $htaccess)) {
                $this->createHtaccess($dr_publish_dir, $this->getHtaccessDepartement($departement), $options['htaccess']);
                $htaccess[$dr_publish_dir] = true;
            }

            // Zip
            $filename_zip = $departement . '_' . $matches['annee'] . '_DR.zip';
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

        $this->logSection('publier', $nb_declaration. ' declaration(s)');
    }

    protected function getPublishDir() {
        $file_dir = sfConfig::get('sf_web_dir') . '/douane/';
        $this->mkdirUnlessFolder($file_dir);
        return $file_dir;
    }

    protected function getHtaccessDepartement($dep) {
        return sprintf(
                "Options +Indexes
Allow from all
Require user viti%s", $dep
        );
    }

    protected function getHtaccessGlobal() {
        return sprintf(
                "Options -Indexes
Deny from all
AuthType Basic
AuthName \"Espace des professionnels du Vignoble d'Alsace\"
AuthUserFile %s
", sfConfig::get('sf_root_dir') . '/htpasswd/viticulteurs');
    }

}
