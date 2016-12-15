<?php

class DRStatsRecolteMairieTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'campagne'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'stats-recolte-mairie';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($arguments['campagne'], acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        $stats = array();
        $n=0;

        foreach ($dr_ids as $id) {
            if (!preg_match("/^DR-(67|68)/", $id)) {

                continue;
            }

            $dr = acCouchdbManager::getClient("DR")->find($id, acCouchdbClient::HYDRATE_JSON);

            if(!isset($dr->validee)) {

                continue;
            }

            $n++;

            if(!isset($dr->recolte->certification->genre)) {

                continue;
            }

            $insee = $dr->declaration_insee;

            if(!$insee) {
                echo sprintf("ERREUR;INSEE MANQUANT;%s\n", $dr->_id);
                exit;
            }

            if(!array_key_exists($insee, $stats)) {
                $stats[$insee]['superficie'] = 0;
                $stats[$insee]['volume'] = 0;
                $stats[$insee]['volume_revendique'] = 0;
                $stats[$insee]['usages_industriels'] = 0;
                $stats[$insee]['appellations'] = array();
            }

            foreach($dr->recolte->certification->genre as $appellation_key => $appellation) {
                if (!preg_match("/^appellation/", $appellation_key)) {

                    continue;
                }
                foreach($appellation as $mention_key => $mention) {
                    if (!preg_match("/^mention/", $mention_key)) {

                        continue;
                    }
                    foreach($mention as $lieu_key => $lieu) {
                        if (!preg_match("/^lieu/", $lieu_key)) {

                            continue;
                        }

                        if(!array_key_exists($appellation_key, $stats[$insee]['appellations'])) {
                            $stats[$insee]['appellations'][$appellation_key]['superficie'] = 0;
                            $stats[$insee]['appellations'][$appellation_key]['volume'] = 0;
                            $stats[$insee]['appellations'][$appellation_key]['volume_revendique'] = 0;
                            $stats[$insee]['appellations'][$appellation_key]['usages_industriels'] = 0;
                            $stats[$insee]['appellations'][$appellation_key]['cepages'] = array();
                        }

                        $stats[$insee]['appellations'][$appellation_key]['volume_revendique'] += $lieu->volume_revendique;
                        $stats[$insee]['appellations'][$appellation_key]['usages_industriels'] += $lieu->usages_industriels;
                        $stats[$insee]['volume_revendique'] += $lieu->volume_revendique;
                        $stats[$insee]['usages_industriels'] += $lieu->usages_industriels;

                        foreach($lieu as $couleur_key => $couleur) {
                            if (!preg_match("/^couleur/", $couleur_key)) {

                                continue;
                            }

                            foreach($couleur as $cepage_key => $cepage) {
                                if (!preg_match("/^cepage/", $cepage_key)) {

                                    continue;
                                }


                                if(!array_key_exists($cepage_key, $stats[$insee]['appellations'][$appellation_key]['cepages'])) {
                                    $stats[$insee]['appellations'][$appellation_key]['cepages'][$cepage_key]['superficie'] = 0;
                                    $stats[$insee]['appellations'][$appellation_key]['cepages'][$cepage_key]['volume'] = 0;
                                }

                                $stats[$insee]['appellations'][$appellation_key]['cepages'][$cepage_key]['superficie'] += $cepage->total_superficie;
                                $stats[$insee]['appellations'][$appellation_key]['cepages'][$cepage_key]['volume'] += $cepage->total_volume;
                            }
                        }
                    }
                }
                $stats[$insee]['appellations'][$appellation_key]['superficie'] += $appellation->total_superficie;
                $stats[$insee]['appellations'][$appellation_key]['volume'] += $appellation->total_volume;
                $stats[$insee]['superficie'] += $appellation->total_superficie;
            }
        }

        echo "insee;appellation;cepage;superficie;volume;volume_revendique;usages_industriels\n";

        foreach($stats as $insee => $stat) {
            foreach($stat['appellations'] as $appellation_key => $appellation) {
                foreach($appellation['cepages'] as $cepage_key => $cepage) {
                    echo sprintf("%s;%s;%s;%01.02f;%01.02f;;\n", $insee, $appellation_key, $cepage_key, $cepage['superficie'],$cepage['volume']);
                }

                echo sprintf("%s;%s;TOTAL;%01.02f;%01.02f;%01.02f;%01.02f\n", $insee, $appellation_key, $appellation['superficie'],$appellation['volume'],$appellation['volume_revendique'],$appellation['usages_industriels']);
            }

            echo sprintf("%s;TOTAL;TOTAL;%01.02f;%01.02f;%01.02f;%01.02f\n", $insee, $stat['superficie'],$stat['volume'],$stat['volume_revendique'],$stat['usages_industriels']);

        }

        echo sprintf("NB_DR;%s",$n)."\n";
    }
}
