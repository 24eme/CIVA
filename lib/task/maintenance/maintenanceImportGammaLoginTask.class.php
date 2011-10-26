<?php

class maintenanceImportGammaLoginTask extends sfBaseTask {

    protected function configure() {
        // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'Fichier gamma de logins'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'import-gamma-login';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenanceImportGammaLogin|INFO] task does things.
Call it with:

  [php symfony maintenanceImportGammaLogin|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        if (!file_exists($arguments['file'])) {
            throw new sfCommandException("the file given can not be found");
        }
        
        $accises_cotisant = array();        
        foreach (file($arguments['file']) as $numero => $ligne) {
            $tab = explode(";", str_replace("\n", "", $ligne));
            if (count($tab) == 2 && preg_match("/^FR/", $tab[0])) {
                $accises_cotisant[$tab[0]] = $tab[1];
            }
        }
        
        $mets = sfCouchdbManager::getClient("MetteurEnMarche")->getAll(sfCouchdbClient::HYDRATE_JSON);
        $nb_find = 0;
        foreach($mets as $met) {
            if(isset($met->gamma)) {
                    $m = sfCouchdbManager::getClient()->retrieveDocumentById($met->_id);
                    $m->remove('gamma');
                    $m->add('gamma');
                    $m->gamma->statut = 'INSCRIT';
                    $m->save();
                    $this->logSection($met->civaba, "updated field gamma");
            }
            
            if (array_key_exists($met->no_accises, $accises_cotisant)) {
                if(!isset($met->gamma)) {
                    $this->logSection($met->civaba, "not inscrit in couchdb", null, "ERROR");
                } else {
                    $nb_find++;
                    $m = sfCouchdbManager::getClient()->retrieveDocumentById($met->_id);
                    $m->gamma->num_cotisant = $tab[1];
                    $m->save();
                    $this->logSection($met->civaba, "updated num_cotisant");
                }
            }
        }
        $this->logSection("find", $nb_find);
    }

}
