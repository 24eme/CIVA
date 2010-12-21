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

        $ids = sfCouchdbManager::getClient('Tiers')->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        $tiers_gamma = array();

        $nb = 0;
        foreach($ids as $id) {
            $tiers = sfCouchdbManager::getClient('Tiers')->retrieveDocumentById($id);
            if ($tiers->hasNoAssices()) {
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
                        $tiers_gamma[$key]['legende'] = array("CVI",
                                                     "Intitulé",
                                                     "Nom Prénom",
                                                     "Adresse",
                                                     "Code Postal",
                                                     "Commune",
                                                     "N° d'Accises",
                                                     "Identifiant",
                                                     "Mot de passe");
                    }
                    $tiers_gamma[$key][$tiers->cvi] = array();
                    $tiers_gamma[$key][$tiers->cvi][] = $tiers->cvi;
                    $tiers_gamma[$key][$tiers->cvi][] = $tiers->intitule;
                    $tiers_gamma[$key][$tiers->cvi][] = $tiers->nom;
                    $tiers_gamma[$key][$tiers->cvi][] = $tiers->siege->adresse;
                    $tiers_gamma[$key][$tiers->cvi][] = $tiers->siege->code_postal;
                    $tiers_gamma[$key][$tiers->cvi][] = $tiers->siege->commune;
                    $tiers_gamma[$key][$tiers->cvi][] = $tiers->no_accises;
                    if (!$tiers->isInscrit()) {
                        $tiers_gamma[$key][$tiers->cvi][] = $tiers->cvi;
                        $tiers_gamma[$key][$tiers->cvi][] = str_replace('{TEXT}', '', $tiers->mot_de_passe);
                    } else {
                        $tiers_gamma[$key][$tiers->cvi][] = '';
                        $tiers_gamma[$key][$tiers->cvi][] = '';
                    }
                }

            }
        }

        foreach($tiers_gamma as $key => $item) {
            $content_csv = Tools::getCsvFromArray($item);
            $filedir = sfConfig::get('sf_web_dir').'/';
            $filename = 'GAMMA-'.  strtoupper($key).'.csv';
            file_put_contents($filedir.$filename, $content_csv);
            $this->logSection("created", $filedir.$filename);
        }

        $this->logSection("done", $nb);
    }
}
