<?php

class importConfiguration2013Task extends sfBaseTask
{

    protected $cepage_order = array("CH", "SY", "AU", "PB", "PI", "ED", "RI", "PG", "MU", "MO", "GW");
    
    protected function configure()
    {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
            new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'couchdb'),
        ));

        $this->namespace = 'import';
        $this->name = 'Configuration2013';
        $this->briefDescription = 'import csv achat file';
        $this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $json = new stdClass();
        $json->_id = 'CONFIGURATION-2013';
        $json->type = 'Configuration';
        $json->campagne = '2013';
        $json->virtual = "2012";
        $docs[] = $json;

        if ($options['import'] == 'couchdb') {
          foreach ($docs as $data) {
            $doc = acCouchdbManager::getClient("DR")->find($data->_id, acCouchdbClient::HYDRATE_JSON);
            if ($doc) {
                acCouchdbManager::getClient()->deleteDoc($doc);
            }
            if (isset($data->delete))
              continue;
                $doc = acCouchdbManager::getClient()->createDocumentFromData($data);
            $doc->save();
          }
          return;
        }
        echo '{"docs":';
        echo json_encode($docs);
        echo '}';
    }

}