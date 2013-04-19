<?php

class maintenanceExportTiersGammaTask extends sfBaseTask {
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

        $this->namespace        = 'maintenance';
        $this->name             = 'export-tiers-gamma';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [setTiersPassword|INFO] task does things.
Call it with:

  [php symfony maintenanceExportTiersGammaTask|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $ids_cvi = acCouchdbManager::getClient('Tiers')->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        $ids_civaba = acCouchdbManager::getClient('Tiers')->getAllCivaba(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        $ids = array_merge($ids_cvi, $ids_civaba);
        
        $tiers_gamma = array();

        $nb = 0;
        $nb_unknow = 0;
        foreach($ids as $id) {
            $tiers = acCouchdbManager::getClient('Tiers')->find($id);
            if ($tiers->hasNoAssices() && $tiers->cvi != "7523700100") {
                $key = null;
                if ($tiers->isInscrit() && $tiers->recoltant == 1) {
                    $key = 'inscrit_recoltant';
                } elseif(!$tiers->isInscrit() && $tiers->recoltant == 1) {
                    $key = 'non_inscrit_recoltant';
                } elseif(!$tiers->isInscrit() && $tiers->recoltant == 0) {
                    $key = 'non_inscrit_non_recoltant';
                }
                if ($key) {
                    $nb++;
                    if (!array_key_exists($key, $tiers_gamma)) {
                        $tiers_gamma[$key] = array();
                        $tiers_gamma[$key]['legende'] = array("CIVABA",
                                                     "CVI",
                                                     "Intitulé",
                                                     "Nom Prénom",
                                                     "Adresse",
                                                     "Code Postal",
                                                     "Commune",
                                                     "N° d'Accises",
                                                     "Identifiant",
                                                     "Mot de passe");
                    }
                    $key_item = $tiers->civaba.'_'.$tiers->cvi;
                    if (array_key_exists($key_item, $tiers_gamma[$key])) {
                        $this->log($tiers->cvi);
                        $this->log($tiers_gamma[$key][$key_item][1]);
                    }
                    $tiers_gamma[$key][$key_item] = array();
                    $tiers_gamma[$key][$key_item][] = $tiers->civaba;
                    if ($tiers->hasNoCvi()) {
                       $tiers_gamma[$key][$key_item][] = '';
                    } else {
                       $tiers_gamma[$key][$key_item][] = $tiers->cvi;
                    }
                    $tiers_gamma[$key][$key_item][] = $tiers->intitule;
                    $tiers_gamma[$key][$key_item][] = $tiers->nom;
                    $tiers_gamma[$key][$key_item][] = $tiers->siege->adresse;
                    $tiers_gamma[$key][$key_item][] = $tiers->siege->code_postal;
                    $tiers_gamma[$key][$key_item][] = $tiers->siege->commune;
                    $tiers_gamma[$key][$key_item][] = $tiers->no_accises;
                    if (!$tiers->isInscrit()) {
                        $tiers_gamma[$key][$key_item][] = $tiers->cvi;
                        $tiers_gamma[$key][$key_item][] = str_replace('{TEXT}', '', $tiers->mot_de_passe);
                    } else {
                        $tiers_gamma[$key][$key_item][] = '';
                        $tiers_gamma[$key][$key_item][] = '';
                    }
                } else {
                    $nb_unknow++;
                }
            }
        }

        $global_contents = '';
        $filedir = sfConfig::get('sf_web_dir').'/';
        foreach($tiers_gamma as $key => $item) {
            $content_csv = Tools::getCsvFromArray($item);
            $global_contents .= $content_csv;
            $filename = 'GAMMA-'.  strtoupper($key).'.csv';
            file_put_contents($filedir.$filename, $content_csv);
            $this->logSection("created", $filedir.$filename);
        }

        $filename = 'GAMMA-TOUS.csv';
        file_put_contents($filedir.$filename, $global_contents);
        $this->logSection("created", $filedir.$filename);

        $this->logSection("done", $nb);
        $this->logSection("done", $nb_unknow);
    }
}
