<?php

class DSStatsStocksTask extends sfBaseTask
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

        $exportManager = new ExportDSCiva($arguments['campagne']);

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

        foreach($stats['appellations'] as $appellation_key => $appellation) {
            foreach($appellation['cepages'] as $cepage_key => $cepage) {
                echo sprintf($ligne, $appellation_key, 
                                     $cepage_key, 
                                     $cepage['volume_total'],
                                     $cepage['volume_normal'],
                                     $cepage['volume_vt'],
                                     $cepage['volume_sgn']);
            }

            echo sprintf($ligne, $appellation_key, 
                                 "TOTAL", 
                                 $appellation['volume_total'],
                                 $appellation['volume_normal'],
                                 $appellation['volume_vt'],
                                 $appellation['volume_sgn']);
        }

        echo sprintf($ligne, "TOTAL", 
                             "TOTAL", 
                             $stats['volume_total'],
                             $stats['volume_normal'],
                             $stats['volume_vt'],
                             $stats['volume_sgn']);
    }
}