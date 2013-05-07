<?php

class importAchatTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'My import from file'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
        ));

        $this->namespace = 'import';
        $this->name = 'Achat';
        $this->briefDescription = 'import csv achat file';
        $this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) 
    {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        
        foreach (file($arguments['file']) as $a) {
            $json = new stdClass();
            $db2 = new Db2Achat(explode(',', preg_replace('/"/', '', $a)));

            if (!$db2->get(Db2Achat::COL_CVI) ||
                    !strlen($db2->get(Db2Achat::COL_CVI)) ||
                    ($db2->get(Db2Achat::COL_QUALITE) != 'C' &&
                    $db2->get(Db2Achat::COL_QUALITE) != 'N' &&
                    $db2->get(Db2Achat::COL_QUALITE) != 'X' )) {
                continue;
            }
            
            $acheteur = acCouchdbManager::getClient('Acheteur')->retrieveByCvi($db2->get(Db2Achat::COL_CVI));
            
            if (!$acheteur) {
                $acheteur = new Acheteur();
                $acheteur->set('_id', 'ACHAT-'.$db2->get(Db2Achat::COL_CVI));
            }

            $acheteur->cvi = $db2->get(Db2Achat::COL_CVI);
            $acheteur->civaba = $db2->get(Db2Achat::COL_CIVABA);

            if ($db2->get(Db2Achat::COL_QUALITE) == 'N') {
                $acheteur->qualite = Acheteur::ACHETEUR_NEGOCIANT;
            } else if ($db2->get(Db2Achat::COL_QUALITE) == 'C') {
                $acheteur->qualite = Acheteur::ACHETEUR_COOPERATIVE;
            } elseif ($db2->get(Db2Achat::COL_QUALITE) == 'X') {
                $acheteur->qualite = Acheteur::ACHETEUR_NEGOCAVE;
            }
            $acheteur->nom = rtrim(preg_replace('/\s{4}\s*/', ', ', $db2->get(Db2Achat::COL_NOM)));
            $acheteur->commune = rtrim($db2->get(Db2Achat::COL_COMMUNE));
            //$acheteur->db2->num = $db2->get(Db2Achat::COL_NUM);
            
            if($acheteur->isModified()) {
                $acheteur->db2->import_date = date("Y-m-d");
            }

            if($acheteur->isNew()) {
               $this->logSection("new", $acheteur->get('_id')); 
            } elseif($acheteur->isModified()) {
               $this->logSection("modified", $acheteur->get('_id'));  
            }
            
            $acheteur->save();
        }
    }

    private function recode_number($val) {
        return preg_replace('/^\./', '0.', $val) + 0;
    }

}
