<?php

class importTiers2Task extends sfBaseTask {

    protected $_tiers_collection = array();
    protected $_insee = null;

    protected function configure() {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
            new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'couchdb'),
            new sfCommandOption('removedb', null, sfCommandOption::PARAMETER_REQUIRED, '= yes if remove the db debore import [yes|no]', 'no'),
            new sfCommandOption('file', null, sfCommandOption::PARAMETER_REQUIRED, 'import from file', sfConfig::get('sf_data_dir') . '/import/Tiers-maj-20110512'),
            new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'year', '09'),
        ));

        $this->namespace = 'import';
        $this->name = 'Tiers2';
        $this->briefDescription = 'import csv tiers file';
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        /*if ($options['removedb'] == 'yes' && $options['import'] == 'couchdb') {
            if (sfCouchdbManager::getClient()->databaseExists()) {
                sfCouchdbManager::getClient()->deleteDatabase();
            }
            sfCouchdbManager::getClient()->createDatabase();
        }*/

        $nb_updated = 0;
        $nb_add = 0;
        $nb_not_updated = 0;
        $tiers_added_cvis = array();
        
        $tiers_collection = array();
        $this->logSection('use file', $options['file']);
        foreach (file($options['file']) as $a) {
            $item = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $a)));
            if (!array_key_exists($item[3], $tiers_collection)) {
                $tiers_collection[$item[3]] = array();
            }
            $tiers_collection[$item[3]][] = $item;
        }

        foreach ($tiers_collection as $no_stock => $items) {
            if (count($items) == 1) {
                $this->addOneTiers($items[0]);
            } elseif (count($items) == 2) {
                $this->addTwoTiers($items[0], $items[1]);
            } elseif (count($items) > 2) {
                $this->addMultipleTiers($items);
            }
        }

        foreach ($this->_tiers_collection as $tiers) {
            $action = $this->saveTiers($tiers);
            if ($action == 1) {
                $nb_add++;
                $tiers_added_cvis[] = $tiers[57];
            } elseif($action == 2) {
                $nb_updated++;
            } elseif($action == 3) {
                $nb_not_updated++;
            }
        }

        $this->logSection("added", $nb_add);
        $this->logSection("updated", $nb_updated);
        $this->logSection("not updated", $nb_not_updated);
        $this->logSection("added cvis", implode(', ',$tiers_added_cvis));       
    }

    private function getInsee() {
        if (is_null($this->_insee)) {
            $csv = array();
            $this->_insee = array();
            foreach (file(sfConfig::get('sf_data_dir') . '/import/Commune') as $c) {
                $csv = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
                $this->_insee[$csv[0]] = $csv[1];
            }
        }
        return $this->_insee;
    }

    private function isRecoltant($tiers) {
        if ($tiers[57]) {
            return true;
        } else {
            return false;
        }
    }

    private function isJustRecoltant($tiers) {
        return $this->isRecoltant($tiers) && !$this->isMetteurEnMarche($tiers);
    }

    private function isMetteurEnMarche($tiers) {
        if ($tiers[1]) {
            return true;
        } else {
            return false;
        }
    }

    private function isJustMetteurEnMarche($tiers) {
        return!$this->isRecoltant($tiers) && $this->isMetteurEnMarche($tiers);
    }

    private function isRecoltantMetteurenMarche($tiers) {
        return $this->isRecoltant($tiers) && $this->isMetteurEnMarche($tiers);
    }

    private function combinerRecoltantMetteurEnMarche($recoltant, $metteur_marche) {       
        $index_keep_tiers_stock = array(10, 1, 9, 37, 39, 40, 82, 41, 42, 12, 13, 14, 15, 38, 8, 69, 68, 70);
        foreach ($index_keep_tiers_stock as $index) {
            if ((!isset($recoltant[$index]) || !$recoltant[$index]) && isset($metteur_marche[$index]) && $metteur_marche[$index]) {
                $recoltant[$index] = $metteur_marche[$index];
            }
        }
        $recoltant[99] = $recoltant[57];
        $recoltant['metteur_marche'] = $metteur_marche;
        return $recoltant;
    }

    private function combinerRecoltant($recoltant_source, $recoltants_suppl) {
        $index_keep_tiers_stock = array(37, 39, 40, 82);

        foreach ($index_keep_tiers_stock as $index) {
            foreach($recoltants_suppl as $recoltant_suppl) {
                if ((!isset($recoltant_source[$index]) || !$recoltant_source[$index]) && isset($recoltant_suppl[$index]) && $recoltant_suppl[$index]) {
                    $recoltant_source[$index] = $recoltant_suppl[$index];
                }
            }
        }
        return $recoltant_source;
    }

    private function addMetteurMarche($tiers) {
        $tiers[57] = 'C' . $tiers[1];
        $this->_tiers_collection[] = $tiers;
    }

    private function addRecoltant($tiers, $autres_tiers = array()) {
        $this->_tiers_collection[] = $this->combinerRecoltant($tiers, $autres_tiers);
    }

    private function addOneTiers($tiers, $autres_tiers = array()) {
        if ($this->isRecoltant($tiers)) {
            $this->addRecoltant($tiers, $autres_tiers);
        } elseif ($this->isJustMetteurEnMarche($tiers)) {
            $this->addMetteurMarche($tiers);
        }
    }

    private function addTwoTiers($tiers1, $tiers2, $autres_tiers = array()) {
        if ($this->isJustRecoltant($tiers1) && $this->isJustMetteurEnMarche($tiers2)) {
            $this->addRecoltant($this->combinerRecoltantMetteurEnMarche($tiers1, $tiers2), $autres_tiers);
        } elseif($this->isJustRecoltant($tiers2) && $this->isJustMetteurEnMarche($tiers1)) {
            $this->addRecoltant($this->combinerRecoltantMetteurEnMarche($tiers2, $tiers1), $autres_tiers);
        } else {
            $this->addOneTiers($tiers1, array_merge(array($tiers2), $autres_tiers));
            $this->addOneTiers($tiers2, array_merge(array($tiers1), $autres_tiers));
        }
    }

    private function addMultipleTiers($items) {
        $items_just_recoltant = array();
        $item_just_metteur_marche = null;
        $items_just_metteur_marche = array();
        $items_other = array();
        foreach ($items as $item) {
            if ($this->isJustRecoltant($item)) {
                $items_just_recoltant[] = $item;
            } elseif ($this->isJustMetteurEnMarche($item)) {
                $items_just_metteur_marche[] = $item;
            } else {
                $items_other[] = $item;
            }
        }
        if (count($items_just_recoltant) > 0 && count($items_just_metteur_marche) > 0) {
            $item_just_metteur_marche = $items_just_metteur_marche[0];
            foreach ($items_just_metteur_marche as $key => $item) {
                if ($item[0] == $item[3] && $item_just_metteur_marche[0] != $item[0]) {
                    $items_other[] = $item_just_metteur_marche;
                    $item_just_metteur_marche = $item;
                } elseif($item_just_metteur_marche[0] != $item[0]) {
                   $items_other[] = $item;
                }
            }
            foreach ($items_just_recoltant as $item) {
                $this->addTwoTiers($item, $item_just_metteur_marche, $items);
            }
        }

        foreach ($items_other as $item) {
            $this->addOneTiers($item, $items);
        }
    }

    private function saveTiers($tiers) {
        $modifications = 0;
        $insee = $this->getInsee();
        
        $tiers_metteur_marche = null;
        if (isset($tiers['metteur_marche'])) {
            $tiers_metteur_marche = $tiers['metteur_marche'];
        }

        $tiers_object = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($tiers[57]);

        if (!$tiers_object) {
            $tiers_object = new Tiers();
            $tiers_object->set('_id', "TIERS-" . $tiers[57]);
            $tiers_object->cvi = $tiers[57];
            $tiers_object->mot_de_passe = $this->generatePass();
        }

        if ($tiers[40]) {
            $modifications += $this->checkModification($tiers_object->email, $tiers[40], 'email', !$tiers_object->isNew());
            $tiers_object->email = $tiers[40];
        } else {
            $modifications += $this->checkModification($tiers_object->email, null, 'email', !$tiers_object->isNew());
            $tiers_object->email = null;
        }

        $modifications += $this->checkModification($tiers_object->num, $tiers[0], 'num', !$tiers_object->isNew());
        $tiers_object->num = $tiers[0];

        $modifications += $this->checkModification($tiers_object->no_stock, $tiers[3], 'no_stock', !$tiers_object->isNew());
        $tiers_object->no_stock = $tiers[3];

        $modifications += $this->checkModification($tiers_object->maison_mere, $tiers[10], 'maison_mere', !$tiers_object->isNew());
        $tiers_object->maison_mere = $tiers[10];

        $modifications += $this->checkModification($tiers_object->civaba, $tiers[1], 'civaba', !$tiers_object->isNew());
        $tiers_object->civaba = $tiers[1];

        $modifications += $this->checkModification($tiers_object->no_accises, $tiers[70], 'no_accises', !$tiers_object->isNew());
        $tiers_object->no_accises = $tiers[70];

        $modifications += $this->checkModification($tiers_object->siret, $tiers[58] . '', 'siret', !$tiers_object->isNew());
        $tiers_object->siret = $tiers[58] . '';

        $modifications += $this->checkModification($tiers_object->intitule, $tiers[9], 'intitule', !$tiers_object->isNew());
        $tiers_object->intitule = $tiers[9];
        
        $tiers_object->regime_fiscal = '';

        $modifications += $this->checkModification($tiers_object->nom, preg_replace('/ +/', ' ', $tiers[6]), 'nom', !$tiers_object->isNew());
        $tiers_object->nom = preg_replace('/ +/', ' ', $tiers[6]);

        $modifications += $this->checkModification($tiers_object->declaration_insee, $tiers[62], 'declaration_insee', !$tiers_object->isNew());
        $tiers_object->declaration_insee = $tiers[62];
        if ($tiers[62]) {
            $tiers_object->declaration_commune = $insee[$tiers[62]];
        }

        $modifications += $this->checkModification($tiers_object->siege->adresse, $tiers[46], 'siege/adresse', !$tiers_object->isNew());
        $tiers_object->siege->adresse = $tiers[46];

        $modifications += $this->checkModification($tiers_object->siege->insee_commune, $tiers[59], 'siege/insee_commune', !$tiers_object->isNew());
        $tiers_object->siege->insee_commune = $tiers[59];

        $modifications += $this->checkModification($tiers_object->siege->code_postal, $tiers[60], 'siege/code_postal', !$tiers_object->isNew());
        $tiers_object->siege->code_postal = $tiers[60];

        $modifications += $this->checkModification($tiers_object->siege->commune, $tiers[61], 'siege/commune', !$tiers_object->isNew());
        $tiers_object->siege->commune = $tiers[61];

        /*if (isset($tiers[99])) {
            $modifications += $this->checkModification($tiers_object->cvi_acheteur, $tiers[99], 'cvi_acheteur', !$tiers_object->isNew());
            $tiers_object->cvi_acheteur = $tiers[99];
        } else {
            $modifications += $this->checkModification($tiers_object->cvi_acheteur, null, 'cvi_acheteur', !$tiers_object->isNew());
            $tiers_object->cvi_acheteur = null;
        }*/

        $tiers_object->cvi_acheteur = null;
        
        if ($tiers[37]) {
            $modifications += $this->checkModification($tiers_object->telephone, sprintf('%010d', $tiers[37]), 'telephone', !$tiers_object->isNew());
            $tiers_object->telephone = sprintf('%010d', $tiers[37]);
        } else {
            $modifications += $this->checkModification($tiers_object->telephone, null, 'telephone', !$tiers_object->isNew());
            $tiers_object->telephone = null;
        }

        if ($tiers[39]) {
            $modifications += $this->checkModification($tiers_object->fax, sprintf('%010d', $tiers[39]), 'fax', !$tiers_object->isNew());
            $tiers_object->fax = sprintf('%010d', $tiers[39]);
        } else {
            $modifications += $this->checkModification($tiers_object->fax, null, 'fax', !$tiers_object->isNew());
            $tiers_object->fax = null;
        }

        if (isset($tiers[82]) && $tiers[82]) {
            $modifications += $this->checkModification($tiers_object->web, $tiers[82], 'web', !$tiers_object->isNew());
            $tiers_object->web = $tiers[82];
        } else {
            $modifications += $this->checkModification($tiers_object->web, null, 'web', !$tiers_object->isNew());
            $tiers_object->web = null;
        }

        $modifications += $this->checkModification($tiers_object->exploitant->sexe, $tiers[41], 'exploitant/sexe', !$tiers_object->isNew());
        $tiers_object->exploitant->sexe = $tiers[41];

        if ($tiers[42]) {
            $modifications += $this->checkModification($tiers_object->exploitant->_get('nom'), $tiers[42], 'exploitant/nom', !$tiers_object->isNew());
            $tiers_object->exploitant->nom = $tiers[42];
        } else {
            $modifications += $this->checkModification($tiers_object->exploitant->_get('nom'), $tiers_object->nom, 'exploitant/nom', !$tiers_object->isNew());
            $tiers_object->exploitant->nom = $tiers_object->nom;
        }
        if ($tiers[13]) {
            $modifications += $this->checkModification($tiers_object->exploitant->_get('adresse'), $tiers[12] . ", " . $tiers[13], 'exploitant/adresse', !$tiers_object->isNew());
            $tiers_object->exploitant->adresse = $tiers[12] . ", " . $tiers[13];

            $modifications += $this->checkModification($tiers_object->exploitant->_get('code_postal'), $tiers[15], 'exploitant/code_postal', !$tiers_object->isNew());
            $tiers_object->exploitant->code_postal = $tiers[15];

            $modifications += $this->checkModification($tiers_object->exploitant->_get('commune'), $tiers[14], 'exploitant/commune', !$tiers_object->isNew());
            $tiers_object->exploitant->commune = $tiers[14];
        } else {
            $modifications += $this->checkModification($tiers_object->exploitant->_get('adresse'), $tiers_object->siege->adresse, 'exploitant/adresse', !$tiers_object->isNew());
            $tiers_object->exploitant->adresse = $tiers_object->siege->adresse;

            $modifications += $this->checkModification($tiers_object->exploitant->_get('code_postal'), $tiers_object->siege->code_postal, 'exploitant/code_postal', !$tiers_object->isNew());
            $tiers_object->exploitant->code_postal = $tiers_object->siege->code_postal;

            $modifications += $this->checkModification($tiers_object->exploitant->_get('commune'), $tiers_object->siege->commune, 'exploitant/commune', !$tiers_object->isNew());
            $tiers_object->exploitant->commune = $tiers_object->siege->commune;
        }
        if ($tiers[38]) {
            $modifications += $this->checkModification($tiers_object->exploitant->telephone, sprintf('%010d', $tiers[38]), 'exploitant/telephone', !$tiers_object->isNew());
            $tiers_object->exploitant->telephone = sprintf('%010d', $tiers[38]);
        } else {
            $modifications += $this->checkModification($tiers_object->exploitant->telephone, $tiers_object->telephone, 'exploitant/telephone', !$tiers_object->isNew());
            $tiers_object->exploitant->telephone = $tiers_object->telephone;
        }

        $modifications += $this->checkModification($tiers_object->exploitant->date_naissance, sprintf("%04d-%02d-%02d", $tiers[8], $tiers[69], $tiers[68]), 'exploitant/date_naissance', !$tiers_object->isNew());
        $tiers_object->exploitant->date_naissance = sprintf("%04d-%02d-%02d", $tiers[8], $tiers[69], $tiers[68]);

        if ($tiers[23] == "O") {
            $modifications += $this->checkModification($tiers_object->recoltant, 1, 'recoltant', !$tiers_object->isNew());
            $tiers_object->recoltant = 1;
        } else {
            $modifications += $this->checkModification($tiers_object->recoltant, 0, 'recoltant', !$tiers_object->isNew());
            $tiers_object->recoltant = 0;
        }

        if ($tiers_metteur_marche) {
            $tiers_object->add('metteur_marche');
            $modifications += $this->checkModification($tiers_object->metteur_marche->nom, preg_replace('/ +/', ' ', $tiers_metteur_marche[6]), 'metteur_marche/nom', !$tiers_object->isNew());
            $modifications += $this->checkModification($tiers_object->metteur_marche->num, $tiers_metteur_marche[0], 'metteur_marche/num', !$tiers_object->isNew());
            $tiers_object->metteur_marche->nom = preg_replace('/ +/', ' ', $tiers_metteur_marche[6]);
            $tiers_object->metteur_marche->num = preg_replace('/ +/', ' ', $tiers_metteur_marche[0]);
        } elseif($tiers_object->exist('metteur_marche')) {
            $modifications += $this->checkModification(true, false, 'metteur_marche', !$tiers_object->isNew());
            $tiers_object->remove('metteur_marche');
        }

        if ($tiers_object->isNew()) {
            $this->logSection($tiers_object->cvi, 'added');
        } elseif($modifications > 0) {
            $this->logSection($tiers_object->cvi, 'updated');
        }

        $tiers_object->add('import_db2_date', date('Y-m-d'));
        $tiers_object->add('export_db2_revision', $tiers_object->get('_rev'));

        $is_new = $tiers_object->isNew();
        
        $tiers_object->save();

        unset($tiers_object);

        if ($is_new) {
            return 1;
        } elseif($modifications) {
            return 2;
        } else  {
            return 3;
        }
    }

    private function checkModification($old, $new, $key, $display) {
        if($old != $new) {
            if ($display) {
                $this->logSection($key,'[OLD] '.$old.' / [NEW] '.$new);
            }
            return 1;
        }
        return 0;
    }

    private function recode_number($val) {
        return preg_replace('/^\./', '0.', $val) + 0;
    }

    private function generatePass() {
        return sprintf("{TEXT}%04d", rand(0, 9999));
    }

}
