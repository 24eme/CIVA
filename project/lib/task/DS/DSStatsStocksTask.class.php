<?php

class DSStatsStocksTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('periode', sfCommandArgument::REQUIRED, 'periode'),
            new sfCommandArgument('type_ds', sfCommandArgument::REQUIRED, 'propriete ou negoce'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'ds';
        $this->name = 'stats-stocks';
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

        if(!in_array($arguments['type_ds'], array(DSCivaClient::TYPE_DS_PROPRIETE, DSCivaClient::TYPE_DS_NEGOCE))) {

            throw new sfException("type ds must be propriete ou negoce");
        }

        $exportManager = new ExportDSCiva($arguments['periode'], array($arguments['type_ds']));

        $campagne = substr($arguments['periode'], 0, 4);

        $dss = $exportManager->getDSListe();

        $stats = array();
        $stats['volume_total'] = 0;
        $stats['volume_normal'] = 0;
        $stats['volume_vt'] = 0;
        $stats['volume_sgn'] = 0;
        $stats['appellations'] = array();

        foreach($dss as $ds) {
            $stats['volume_total'] += $ds->declaration->getTotalStock();
            $stats['volume_normal'] += $ds->declaration->getTotalNormal();
            $stats['volume_vt'] += $ds->declaration->getTotalVT();
            $stats['volume_sgn'] += $ds->declaration->getTotalSGN();
            foreach($ds->declaration->getAppellationsSorted() as $appellation) {
            	$appellationKey = $appellation->getKey();
                if(!array_key_exists($appellationKey, $stats['appellations'])) {
                    $stats['appellations'][$appellationKey]['volume_total'] = 0;
                    $stats['appellations'][$appellationKey]['volume_normal'] = 0;
                    $stats['appellations'][$appellationKey]['volume_vt'] = 0;
                    $stats['appellations'][$appellationKey]['volume_sgn'] = 0;
                    $stats['appellations'][$appellationKey]['cepages'] = array();
                }

                $stats['appellations'][$appellationKey]['volume_total'] += $appellation->getTotalStock();
                $stats['appellations'][$appellationKey]['volume_normal'] += $appellation->getTotalNormal();
                $stats['appellations'][$appellationKey]['volume_vt'] += $appellation->getTotalVT();
                $stats['appellations'][$appellationKey]['volume_sgn'] += $appellation->getTotalSGN();

                foreach($appellation->getProduitsSorted() as $cepage) {
            		$cepageKey = $cepage->getKey();
                    if($cepageKey == "cepage_DEFAUT") {
                        $cepageKey = $cepage->getAppellation()->getKey();
                    }
                    if(!array_key_exists($cepageKey, $stats['appellations'][$appellationKey]['cepages'])) {
                        $stats['appellations'][$appellationKey]['cepages'][$cepageKey]['volume_total'] = 0;
                        $stats['appellations'][$appellationKey]['cepages'][$cepageKey]['volume_normal'] = 0;
                        $stats['appellations'][$appellationKey]['cepages'][$cepageKey]['volume_vt'] = 0;
                        $stats['appellations'][$appellationKey]['cepages'][$cepageKey]['volume_sgn'] = 0;
                    }

                    $stats['appellations'][$appellationKey]['cepages'][$cepageKey]['volume_total'] += $cepage->getTotalStock();
                    $stats['appellations'][$appellationKey]['cepages'][$cepageKey]['volume_normal'] += $cepage->getTotalNormal();
                    $stats['appellations'][$appellationKey]['cepages'][$cepageKey]['volume_vt'] += $cepage->getTotalVT();
                    $stats['appellations'][$appellationKey]['cepages'][$cepageKey]['volume_sgn'] += $cepage->getTotalSGN();
                }
            }
        }

        echo "appellation;cepage;volume total;volume normal;volume vt;volume sgn\n";

        $ligne = "%s;%s;%01.02f;%01.02f;%01.02f;%01.02f\n";
        $configuration = ConfigurationClient::getConfiguration($campagne - 1);

        foreach(DSCivaClient::getInstance()->getConfigAppellations($configuration) as $c_appellation) {
            $confAppellationKey = $c_appellation->getKey();
            if(!preg_match('/appellation_/', $c_appellation->getKey()) && $c_appellation instanceof ConfigurationAppellation) {
                $confAppellationKey = 'appellation_'.$confAppellationKey;
            }
            if(!preg_match('/genre/', $c_appellation->getKey()) && $c_appellation instanceof ConfigurationGenre) {
                $confAppellationKey = 'genre'.$confAppellationKey;
            }

            if(!array_key_exists($confAppellationKey, $stats['appellations'])) {
                continue;
            }
            $appellation = $stats['appellations'][$confAppellationKey];

            foreach($c_appellation->getProduits() as $c_cepage) {
            	$confCepageKey = (preg_match('/cepage_/', $c_cepage->getKey()))? $c_cepage->getKey() : 'cepage_'.$c_cepage->getKey();
                if($c_cepage->getKey() == "DEFAUT") {
                    $confCepageKey = "appellation_".$c_cepage->getAppellation()->getKey();
                }
                if(!array_key_exists($confCepageKey, $appellation['cepages'])) {
                    continue;
                }
                $cepage = $appellation['cepages'][$confCepageKey];

                echo sprintf($ligne, $confAppellationKey, 
                                     $confCepageKey, 
                                     $cepage['volume_total'],
                                     $cepage['volume_normal'],
                                     $cepage['volume_vt'],
                                     $cepage['volume_sgn']);

                unset($appellation['cepages'][$confCepageKey]);
            }

            echo sprintf($ligne, $confAppellationKey, 
                                 "TOTAL", 
                                 $appellation['volume_total'],
                                 $appellation['volume_normal'],
                                 $appellation['volume_vt'],
                                 $appellation['volume_sgn']);
            
            unset($stats['appellations'][$confAppellationKey]);
        }

        echo sprintf($ligne, "TOTAL", 
                             "TOTAL", 
                             $stats['volume_total'],
                             $stats['volume_normal'],
                             $stats['volume_vt'],
                             $stats['volume_sgn']);
    }
}