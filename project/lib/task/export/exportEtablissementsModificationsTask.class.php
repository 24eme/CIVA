<?php

class ExportEtablissemenstModificationsTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addArguments(array(

         ));

        $this->addOptions(array(
          new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
          new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
          new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace        = 'export';
        $this->name             = 'etablissements-modifications';
        $this->briefDescription = '';
        $this->detailedDescription = '';
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $etablissementDiff = new EtablissementsDiff();
        $etablissementsDb2 = $etablissementDiff->getEtablissementsDb2();
        $etablissementsCouchdb = $etablissementDiff->getEtablissementsCouchdb();
        $keysIgnored = $etablissementDiff->getKeyIgnored();
        $diff = $etablissementDiff->getDiff();

        echo "N° DB2;Famille;Statut;Intitulé;Raison sociale;CVI;CIVABA;SIRET;Accises;Adresse;Code postal;Commune;Téléphone;Fax;Email;Exploitant Civilité;Exploitant Nom;Exploitant Adresse;Exploitant Code postal;Exploitant Commune;Exploitant Télephone;Exploitant date de naissance\n";

        foreach($diff as $id => $null) {
            $line = "";
            $etablissementDb2 = (isset($etablissementsDb2[$id])) ? $etablissementsDb2[$id] : null;
            $etablissementCouchdb = (isset($etablissementsCouchdb[$id])) ? $etablissementsCouchdb[$id] : null;
            $etablissementReference = ($etablissementCouchdb) ? $etablissementCouchdb : $etablissementDb2;
            foreach($etablissementReference as $key => $null) {
                if(in_array($key, $keysIgnored)) {
                    continue;
                }
                $isDiff = (trim($etablissementDb2[$key]) != trim($etablissementCouchdb[$key]));
                $field = $etablissementCouchdb[$key];
                if($isDiff) {
                    $field = "*" . str_replace("\n", " ", $field) . " (".str_replace("\n", " ", $etablissementDb2[$key]).")";
                }
                $line .= '"'.$field.'";';
            }

            echo $line."\n";
        }
    }
}
