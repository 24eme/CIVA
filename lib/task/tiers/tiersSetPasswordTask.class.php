<?php

class tiersSetPasswordTask extends sfBaseTask {
    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
                new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
                new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
                new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace        = 'tiers';
        $this->name             = 'set-password';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [setTiersPassword|INFO] task does things.
Call it with:

  [php symfony setTiersPassword|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $docs = sfCouchdbManager::getClient('Tiers')->getAll();

        foreach($docs as $id => $anyone) {
            $rec = sfCouchdbManager::getClient()->retrieveDocumentById($id);
            $cpt++;
                        echo $id."\t";
            
            try {
                $rand = $this->generatePass();
                $rec->mot_de_passe = $rand;
                $rec->save();
                echo "DONE\n";
            }catch(Exception $e) {
                sleep(5);
                echo $rand;
                $rec = sfCouchdbManager::getClient()->retrieveDocumentById($id);
                $rec->mot_de_passe = $this->generatePass();
                $rec->save();
                echo "Extra DONE\n";
            }
            if ($cpt > 50) {
                $cpt = 0;
                sleep(1);
            }
        }
    }

    private function generatePass() {
        return sprintf("{TEXT}%04d", rand(0, 9999));
    }


}
