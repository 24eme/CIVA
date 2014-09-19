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
                if(!array_key_exists($appellation->getKey(), $stats['appellations'])) {
                    $stats['appellations'][$appellation->getKey()]['volume_total'] = 0;
                    $stats['appellations'][$appellation->getKey()]['volume_normal'] = 0;
                    $stats['appellations'][$appellation->getKey()]['volume_vt'] = 0;
                    $stats['appellations'][$appellation->getKey()]['volume_sgn'] = 0;
                    $stats['appellations'][$appellation->getKey()]['cepages'] = array();
                }

                $stats['appellations'][$appellation->getKey()]['volume_total'] += $appellation->getTotalStock();
                $stats['appellations'][$appellation->getKey()]['volume_normal'] += $appellation->getTotalNormal();
                $stats['appellations'][$appellation->getKey()]['volume_vt'] += $appellation->getTotalVT();
                $stats['appellations'][$appellation->getKey()]['volume_sgn'] += $appellation->getTotalSGN();

                foreach($appellation->getProduitsSorted() as $cepage) {
                    if(!array_key_exists($cepage->getKey(), $stats['appellations'][$appellation->getKey()]['cepages'])) {
                        $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_total'] = 0;
                        $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_normal'] = 0;
                        $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_vt'] = 0;
                        $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_sgn'] = 0;
                    }

                    $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_total'] += $cepage->getTotalStock();
                    $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_normal'] += $cepage->getTotalNormal();
                    $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_vt'] += $cepage->getTotalVT();
                    $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_sgn'] += $cepage->getTotalSGN();
                }
            }
        }

        echo "appellation;cepage;volume total;volume normal;volume vt;volume sgn\n";

        $ligne = "%s;%s;%01.02f;%01.02f;%01.02f;%01.02f\n";
        $configuration = ConfigurationClient::getConfiguration($campagne - 1);

        foreach($configuration->recolte->getNoeudAppellations() as $c_appellation) {
            if(!array_key_exists($c_appellation->getKey(), $stats['appellations'])) {
                continue;
            }

            $appellation_key = $c_appellation->getKey();
            $appellation = $stats['appellations'][$appellation_key];

            foreach($c_appellation->getProduits() as $c_cepage) {
                if(!array_key_exists($c_cepage->getKey(), $appellation['cepages'])) {
                    continue;
                }

                $cepage_key = $c_cepage->getKey();
                $cepage = $appellation['cepages'][$cepage_key];

                echo sprintf($ligne, $appellation_key, 
                                     $cepage_key, 
                                     $cepage['volume_total'],
                                     $cepage['volume_normal'],
                                     $cepage['volume_vt'],
                                     $cepage['volume_sgn']);

                unset($appellation['cepages'][$cepage_key]);
            }

            echo sprintf($ligne, $appellation_key, 
                                 "TOTAL", 
                                 $appellation['volume_total'],
                                 $appellation['volume_normal'],
                                 $appellation['volume_vt'],
                                 $appellation['volume_sgn']);
            
            unset($stats['appellations'][$appellation_key]);
        }

        echo sprintf($ligne, "TOTAL", 
                             "TOTAL", 
                             $stats['volume_total'],
                             $stats['volume_normal'],
                             $stats['volume_vt'],
                             $stats['volume_sgn']);
    }
}