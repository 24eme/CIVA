<?php

class exportDRAcheteursCsvTask extends sfBaseTask {

    protected $campagne = null;
    
    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Année'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
            new sfCommandOption('debug', null, sfCommandOption::PARAMETER_REQUIRED, 'Debug mode', false),
        ));

        $this->namespace = 'export';
        $this->name = 'dr-acheteurs-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportDRAcheteursCsv|INFO] task does things.
Call it with:

  [php symfony exportDRAcheteursCsv|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        
        $this->campagne = $arguments['campagne'];

        $this->createFileDir();
        $this->cleanFiles();
        
        $acheteurs = sfCouchdbManager::getClient("Acheteur")->getAll($arguments['campagne']);
        foreach($acheteurs as $acheteur) {
            $export = new ExportDRAcheteurCsv($arguments['campagne'], $acheteur, $options['debug']);
            if ($export->hasDR()) {
                file_put_contents($this->getFileDir().'/'.$this->campagne.'_DR_ACHETEUR_'.$acheteur->cvi.'.csv', $export->output());
            }
        }
    }
    
    protected function getFiles() {
        return sfFinder::type('file')->in($this->getFileDir());
    }
    
    protected function cleanFiles() {
        $files = $this->getFiles();
        foreach($files as $file) {
            unlink($file);
        }
    }

    protected function getFileDir() {
        return sfConfig::get('sf_data_dir') . '/export/dr-acheteur/csv/' . $this->campagne;
    }

    protected function createFileDir() {
        if (!file_exists($this->getFileDir())) {
            mkdir(sfConfig::get('sf_data_dir') . '/export/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr-acheteur/');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr-acheteur/csv');
            mkdir(sfConfig::get('sf_data_dir') . '/export/dr-acheteur/csv/' . $this->campagne);
            $this->logSection($this->getFileDir(), 'folder created');
        }
    }

}
