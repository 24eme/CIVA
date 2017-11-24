<?php

class DRStatsRecolteTask extends sfBaseTask
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
        $this->name = 'stats-recolte';
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
        $stats['superficie'] = 0;
        $stats['volume'] = 0;
        $stats['volume_revendique'] = 0;
        $stats['usages_industriels'] = 0;
        $stats['vci'] = 0;
        $stats['appellations'] = array();
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

                        if(!array_key_exists($appellation_key, $stats['appellations'])) {
                            $stats['appellations'][$appellation_key]['superficie'] = 0;
                            $stats['appellations'][$appellation_key]['volume'] = 0;
                            $stats['appellations'][$appellation_key]['volume_revendique'] = 0;
                            $stats['appellations'][$appellation_key]['usages_industriels'] = 0;
                            $stats['appellations'][$appellation_key]['vci'] = 0;
                            $stats['appellations'][$appellation_key]['cepages'] = array();
                        }

                        $stats['appellations'][$appellation_key]['volume_revendique'] += $lieu->volume_revendique;
                        $stats['appellations'][$appellation_key]['usages_industriels'] += $lieu->usages_industriels;
                        if(isset($lieu->vci)) {
                            $stats['appellations'][$appellation_key]['vci'] += $lieu->vci;
                        }
                        $stats['volume_revendique'] += $lieu->volume_revendique;
                        $stats['usages_industriels'] += $lieu->usages_industriels;
                        if (isset($lieu->vci)) {
                            $stats['vci'] += $lieu->vci;
                        }
                        foreach($lieu as $couleur_key => $couleur) {
                            if (!preg_match("/^couleur/", $couleur_key)) {

                                continue;
                            }

                            foreach($couleur as $cepage_key => $cepage) {
                                if (!preg_match("/^cepage/", $cepage_key)) {

                                    continue;
                                }


                                if(!array_key_exists($cepage_key, $stats['appellations'][$appellation_key]['cepages'])) {
                                    $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['superficie'] = 0;
                                    $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['volume'] = 0;
                                    $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['vci'] = 0;
                                }

                                $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['superficie'] += $cepage->total_superficie;
                                $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['volume'] += $cepage->total_volume;
                                if(isset($cepage->vci)) {
                                    $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['vci'] += $cepage->vci;
                                }
                            }
                        }
                    }
                }

                $stats['appellations'][$appellation_key]['superficie'] += $appellation->total_superficie;
                $stats['appellations'][$appellation_key]['volume'] += $appellation->total_volume;
                $stats['superficie'] += $appellation->total_superficie;
                $stats['volume'] += $appellation->total_volume;
            }
        }

        echo "appellation;cepage;superficie;volume;volume_revendique;usages_industriels;vci\n";

        foreach($stats['appellations'] as $appellation_key => $appellation) {
            foreach($appellation['cepages'] as $cepage_key => $cepage) {
                echo sprintf("%s;%s;%01.02f;%01.02f;;;%s\n", $appellation_key, $cepage_key, $cepage['superficie'],$cepage['volume'], ($cepage['vci'] > 0) ? sprintf("%01.02f", $cepage['vci']) : null);
            }

            echo sprintf("%s;TOTAL;%01.02f;%01.02f;%01.02f;%01.02f;%s\n", $appellation_key, $appellation['superficie'],$appellation['volume'],$appellation['volume_revendique'],$appellation['usages_industriels'], ($appellation['vci'] > 0) ? sprintf("%01.02f", $appellation['vci']) : null);
        }

        echo sprintf("TOTAL;TOTAL;%01.02f;%01.02f;%01.02f;%01.02f;%s\n", $stats['superficie'],$stats['volume'],$stats['volume_revendique'],$stats['usages_industriels'], ($stats['vci'] > 0) ? sprintf("%01.02f", $stats['vci']) : null);

        echo sprintf("NB_DR;%s",$n)."\n";
    }
}
