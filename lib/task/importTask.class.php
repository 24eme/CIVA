<?php

class importTask extends sfBaseTask {

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

        $this->namespace = 'civa';
        $this->name = 'import';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        set_time_limit('3600');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        if (sfCouchdbManager::getClient()->databaseExists()) {
            sfCouchdbManager::getClient()->deleteDatabase();
        }
        sfCouchdbManager::getClient()->createDatabase();

        $achat = array();
        $achatcvi = array();
        /* Reconstitution des vendeurs pour chaque récoltant */
        foreach (file(sfConfig::get('sf_data_dir') . '/' . 'Dclven09') as $a) {
            $csv = explode(',', preg_replace('/"/', '', $a));
            $achat[$csv[0]][$csv[1]][$csv[4]][$csv[3]] = $csv[6];
            $achatcvi[$csv[0]][$csv[1]][$csv[4]][] = $csv[6];
        }

        $list_documents = array();
        $max = count(file(sfConfig::get('sf_data_dir') . '/' . "Dcllig09"));
        $nb = 0;
        foreach (file(sfConfig::get('sf_data_dir') . '/' . "Dcllig09") as $l) {
            $csv = explode(',', preg_replace('/"/', '', $l));
            $cvi = $csv[1];
            $campagne = $csv[0];
            $_id = 'DR' . '-' . $cvi . '-' . $campagne;
            $appellation = $csv[3];
            $cepage = $csv[4];

            $doc = new DR();
            if (!isset($list_documents[$_id])) {
                $doc = new DR();
                $doc->set('_id', $_id);
                $doc->setCvi($cvi);
                $doc->setCampagne($campagne);
                foreach($achatcvi[$campagne][$cvi] as $key => $item) {
                    foreach($item as $data) {
                        $doc->getAcheteurs()->add('appellation_'.$key)->add(null,$data);
                    }
                }

                $list_documents[$_id] = $doc;
            } else {
                $doc = $list_documents[$_id];
            }


            if (in_array($cepage, array('LA', 'LR', 'LN', 'LC', 'LM', 'LT', 'LG', 'LE', 'LS'))) { /* Prise en compte des lies */

                if (is_null($this->getLies())) {
                    $this->setLies(0);
                }
                $this->setLies($this->getLies() + $this->recode_number($csv[12]));

            } elseif (in_array($cepage, array('AL', 'CR', 'GD', 'AN', 'LA', 'LR', 'LN', 'LC', 'LM', 'LT', 'LG', 'LE', 'LS', 'VT'))) {

            } elseif ($cepage == 'RB') {

                $rebeche = new DRRecolteAppellationCepageDetail();
                $rebeche->setAppellation($appellation);
                $rebeche->setVolume($this->recode_number($csv[12]));
                $rebeche->setCaveParticuliere($this->recode_number($csv[21]));
                /* les coopératives */
                for ($i = 5; $i < 8; $i++) {
                    $val = $this->recode_number($csv[13 + $i]);
                    if ($val > 0) {
                        $cooperatives = $rebeche->get('cooperatives')->add();
                        $cooperatives->setCvi($achat[$campagne][$cvi][$appellation][$i]);
                        $cooperatives->setQuantiteVendue($val);
                    }
                }
                
                $doc->addRebeche($rebeche);
            } else {

                $detail = new DRRecolteAppellationCepageDetail();
                $detail->setAppellation($appellation);
                $detail->setCepage($cepage);
                $detail->setDenomination($csv[6]);
                $detail->setVtsgn($csv[9]);
                $detail->setCodeLieu($csv[10]);
                $detail->setSurface($this->recode_number($csv[11]));
                $detail->setVolume($this->recode_number($csv[12]));
                $detail->setCaveParticuliere($this->recode_number($csv[21]));
                $detail->setVolumeRevendique($this->recode_number($csv[27]));
                $detail->setVolumeDplc($this->recode_number($csv[28]));
                /* Les acheteurs */
                for ($i = 1; $i < 5; $i++) {
                    $val = $this->recode_number($csv[12 + $i]);
                    if ($val > 0) {
                        $acheteur = $detail->getAcheteurs()->add();
                        $acheteur->setCvi($achat[$campagne][$cvi][$appellation][$i]);
                        $acheteur->setQuantiteVendue($val);
                    }
                }
                /* les coopératives */
                for ($i = 5; $i < 8; $i++) {
                    $val = $this->recode_number($csv[13 + $i]);
                    if ($val > 0) {
                        $cooperatives = $detail->get('cooperatives')->add();
                        $cooperatives->setCvi($achat[$campagne][$cvi][$appellation][$i]);
                        $cooperatives->setQuantiteVendue($val);
                    }
                }
                $doc->addRecolte($detail);

            }
            $this->log($nb++ . '/' . $max);
            if ($nb == 2000) {
                break;
            }
        }

        foreach ($list_documents as $doc) {
            //print_r($doc->getData());
            $doc->save();
        }

        // add your code here
    }

    private function recode_number($val) {
        return preg_replace('/^\./', '0.', $val) + 0;
    }

}
