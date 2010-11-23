<?php

class verifEmailTask extends sfBaseTask {
    protected function configure() {


        $this->addOptions(array(
                new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
                new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
                new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace        = 'maintenance';
        $this->name             = 'verifEmail';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [setTiersPassword|INFO] task does things.
Call it with:

  [php symfony setTiersPassword|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $docs = sfCouchdbManager::getClient('Tiers')->getAll(sfCouchdbClient::HYDRATE_JSON);

        $nb_inscrit = 0;
        $nb_inscrit_ss_email = 0;

        foreach($docs as $id => $rec) {

            if (substr($rec->mot_de_passe, 0, 6) !== "{TEXT}") {
                $nb_inscrit++;
                if (!$rec->email) {
                    $this->logSection('CVI', $rec->cvi);
                }
            }
        }
   
    }


}
