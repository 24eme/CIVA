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

        $tiersCsv = new Db2Tiers2Csv(sfConfig::get('sf_root_dir')."/data/import/Tiers/Tiers-last");
        $etablissementsDb2 = $tiersCsv->getEtablissements();
        $etablissementsCouchdb = array();
        $results = EtablissementClient::getInstance()->startkey(array("INTERPRO-declaration", "ACTIF"))
                            ->endkey(array("INTERPRO-declaration","ACTIF", array()))
                            ->reduce(false)
                            ->getView('etablissement', 'all');
        foreach($results->rows as $row) {
            $etablissement = EtablissementClient::getInstance()->find($row->id);
            if($etablissement->getFamille() == "COURTIER") {
                continue;
            }
            $etablissementsCouchdb[$row->id] = EtablissementCsvFile::export($etablissement);
            if(isset($etablissementsDb2[$row->id])) {
                $etablissementsCouchdb[$row->id][5] = $etablissementsDb2[$row->id][5];
            }
        }

        echo "Identifiant;Famille;Statut;Numéro Tiers;Intitulé;Raison sociale;CVI;CIVABA;SIRET;Accises;Carte pro;Adresse;Code postal;Commune;INSEE;Pays;Déclaration Insee;Déclaration Commune;Téléphone Bureau;Téléphone Perso;Fax;Email;Exploitant Civilité;Exploitant Nom;Exploitant Adresse;Exploitant Code postal;Exploitant Commune;Exploitant Pays;Exploitant Télephone\n";


        $diff = array_diff_assoc_recursive($etablissementsDb2, $etablissementsCouchdb);

        $keysIgnored = array(0,1);

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
                    $field = "*" . $field . " (".$etablissementDb2[$key].")";
                }
                $line .= '"'.$field.'";';
            }

            echo $line."\n";
        }
    }
}

function array_diff_assoc_recursive($array1, $array2) {
    $difference=array();
    foreach($array1 as $key => $value) {
        if( is_array($value) ) {
            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if( !empty($new_diff) )
                    $difference[$key] = $new_diff;
            }
        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}
