<?php

class importTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
         $this->addArguments(array(
           new sfCommandArgument('fast', sfCommandArgument::OPTIONAL, 'Script plus rapide sans validation de schema'),
         ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'couchdb';
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
        
        $fast = false;
        if (isset($arguments['fast']) &&  $arguments['fast'] == 1){
            $fast = true;
        }

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
                if (!$fast) {
                    $doc = new DR();
                } else {
                    $doc = new sfCouchdbDocument($definition);
                }
                $doc->set('_id', $_id);
                $doc->set('cvi', $cvi);
                $doc->set('campagne', $campagne);
                foreach($achatcvi[$campagne][$cvi] as $key => $item) {
                    foreach($item as $data) {
                        $doc->add('acheteurs')->add('appellation_'.$key)->add(null,$data);
                    }
                }
                
                $list_documents[$_id] = $doc;
            } else {
                $doc = $list_documents[$_id];
            }


            if (in_array($cepage, array('LA', 'LR', 'LN', 'LC', 'LM', 'LT', 'LG', 'LE', 'LS'))) { /* Prise en compte des lies */
                
                if (is_null($this->add('lies'))) {
                    $this->set('lies', 0);
                }
                $this->set('lies', $this->get('lies') + $this->recode_number($csv[12]));

            } elseif ($cepage == 'VT') { /* Vin de table */
                
                $doc->add('VT')->set('surface', $csv[11]);
                $doc->add('VT')->set('volume', $csv[12]);

            } elseif (in_array($cepage, array('AL', 'CR', 'GD', 'AN', 'LA', 'LR', 'LN', 'LC', 'LM', 'LT', 'LG', 'LE', 'LS', 'VT'))) {
                
            } elseif ($cepage == 'RB') {
                if (!$fast) {
                    $rebeche = new DRRecolteAppellationRebecheDetail();
                } else {
                    $rebeche = new sfCouchdbJson();
                }
                
                $rebeche->set('appellation', $appellation);
                $rebeche->set('volume', $this->recode_number($csv[12]));
                $rebeche->set('cave_particuliere', $this->recode_number($csv[21]));
                /* les coopératives */
                for ($i = 5; $i < 8; $i++) {
                    $val = $this->recode_number($csv[13 + $i]);
                    if ($val > 0) {
                        $cooperatives = $rebeche->add('cooperatives')->add();
                        $cooperatives->set('cvi', $achat[$campagne][$cvi][$appellation][$i]);
                        $cooperatives->set('quantite_vendue', $val);
                    }
                }
                if (!$fast) {
                    $doc->addRebeche($rebeche);
                } else {
                    $doc->add('recolte')->add('appellation_'.$appellation)->add('lieu')->add('rebeche')->add(null, $rebeche);
                }

            } else {

                $detail = new DRRecolteAppellationCepageDetail();
                $detail->set('appellation', $appellation);
                $detail->set('cepage', $cepage);
                $detail->set('denomination', $csv[6]);
                $detail->set('vtsgn', $csv[9]);
                $detail->set('code_lieu', $csv[10]);
                $detail->set('surface', $this->recode_number($csv[11]));
                $detail->set('volume', $this->recode_number($csv[12]));
                $rebeche->set('cave_particuliere', $this->recode_number($csv[21]));
                $detail->set('volume_revendique', $this->recode_number($csv[27]));
                $detail->set('volume_dplc', $this->recode_number($csv[28]));
                
                /* les acheteurs */
                for ($i = 1; $i < 5; $i++) {
                    $val = $this->recode_number($csv[12 + $i]);
                    if ($val > 0) {
                        $acheteur = $detail->add('acheteurs')->add();
                        $acheteur->set('cvi', $achat[$campagne][$cvi][$appellation][$i]);
                        $acheteur->set('quantite_vendue', $val);
                    }
                }
                /* les coopératives */
                for ($i = 5; $i < 8; $i++) {
                    $val = $this->recode_number($csv[13 + $i]);
                    if ($val > 0) {
                        $cooperatives = $detail->add('cooperatives')->add();
                        $cooperatives->set('cvi', $achat[$campagne][$cvi][$appellation][$i]);
                        $cooperatives->set('quantite_vendue', $val);
                    }
                }
                if (!$fast) {
                    $doc->addRecolte($detail);
                } else {
                    $doc->add('recolte')->add('appellation_'.$appellation)->add('lieu')->add('cepage_'.$cepage)->add('detail')->add(null, $detail);
                }
                

            }
            $this->log($nb++ . '/' . $max);
            if ($nb == 2000) {
                break;
            }
        }

        foreach ($list_documents as $doc) {
            $doc->save();
        }

        // add your code here
    }

    private function recode_number($val) {
        return preg_replace('/^\./', '0.', $val) + 0;
    }

}
