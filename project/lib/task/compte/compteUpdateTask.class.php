<?php

class compteUpdateTask extends sfBaseTask {

    protected $_db2 = array();

    protected function configure() {
        $this->addArguments(array(
                //new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'My import from file'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'compte';
        $this->name = 'update';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [importCompte|INFO] task does things.
Call it with:

  [php symfony importCompte|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();


        $liaisons = sfCouchdbManager::getClient()->group_level(3)->getView("TIERS", "liaison");
        $stocks = array();
        $acheteurs = array();
        foreach ($liaisons->rows as $liaison) {
            if (preg_match('/^ACHAT-/', $liaison->key[2])) {
                $acheteurs[] = sfCouchdbManager::getClient()->retrieveDocumentById($liaison->key[2], sfCouchdbClient::HYDRATE_JSON);

                continue;
            }
            
            $stocks[$liaison->key[0]][$liaison->key[1]][] = sfCouchdbManager::getClient()->retrieveDocumentById($liaison->key[2], sfCouchdbClient::HYDRATE_JSON);
        }

        $comptes = array();
        foreach ($stocks as $no_stock => $nums) {
            $met_en_attente = null;
            $met_en_attente_add = false;
            foreach ($nums as $num => $tiers) {
                if ($num == $no_stock && count($tiers) == 1 && $tiers[0]->type == 'MetteurEnMarche') {
                    $met_en_attente = $tiers[0];
                }
            }
            foreach ($nums as $num => $tiers) {
                if (($met_en_attente && $num == $met_en_attente->db2->num) || $met_en_attente_add) {
                    continue;
                }
                
                if ($met_en_attente && count($tiers) == 1 && $tiers[0]->type == 'Recoltant') {
                    $tiers[] = $met_en_attente;
                    $met_en_attente_add = true;
                }
                
                $comptes[$this->getLogin($tiers)] = $tiers;
            }
            if ($met_en_attente && !$met_en_attente_add) {
                $comptes[$this->getLogin(array($met_en_attente))] = array($met_en_attente);
            }
        }

        foreach($acheteurs as $acheteur) {
           $tiers = array($acheteur);
           $comptes[$this->getLogin($tiers)] = $tiers;     
        }

        // Suppression des comptes inexistants
        $ids_compte = sfCouchdbManager::getClient("_Compte")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        foreach ($ids_compte as $id) {
            $compte = sfCouchdbManager::getClient()->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
            
            if (array_key_exists($compte->login, $comptes) && $compte->type == "CompteProxy") {
                $compte->type = "CompteTiers";
                unset($compte->compte_reference);
                $compte_object = sfCouchdbManager::getClient()->createDocumentFromData($compte);
                $compte_object->save();
                $this->logSection("Compte proxy has been transformed", $compte->_id, null, "ERROR");
            }
            
            if ($compte->type == "CompteTiers") {
                if(!array_key_exists($compte->login, $comptes)) {
                    //Todo : si le compte n'existe en faire un compte proxy

                    //$this->logSection("compte deleted", $compte->_id, null, "ERROR");
                    //sfCouchdbManager::getClient()->deleteDoc($compte);
                }
            }
        }
        
        $tiers_compte = array();

        // Mise à jour ou création des comptes
        foreach ($comptes as $login => $tiers) {
            $compte = sfCouchdbManager::getClient()->retrieveDocumentById('COMPTE-' . $login, sfCouchdbClient::HYDRATE_DOCUMENT);
            if (!$compte) {
                 $compte = new CompteTiers();
                 $compte->set('_id', 'COMPTE-' . $login);
                 $compte->login = $login;
                 $compte->mot_de_passe = $this->generatePass();;
                 $compte->email = $this->combiner($tiers, 'email');
            }

            $email = $this->combiner($tiers, 'email');
            if($email) {
                if ($email != $compte->email && $compte->email) {
                    $this->logSection("L'email a été modifié", $compte->_id);
                }
                $compte->email = $email;
            } elseif(!$email && $compte->email) {
                //$this->logSection("Pas d'email touvé chez les tiers du compte alors qui lui en possède une", $compte->_id);
            }
            
            $compte->db2->no_stock = $tiers[0]->db2->no_stock;

            $compte->remove("tiers");
            $compte->add("tiers");

            $inactif = false;

            foreach ($tiers as $t) {
                $tiers_compte[$t->_id][] = $compte->_id;
                $obj = $compte->tiers->add($t->_id);
                $obj->id = $t->_id;
                $obj->type = $t->type;
                $obj->nom = $t->nom;
                $inactif = (isset($t->statut) && $t->statut == _TiersClient::STATUT_INACTIF);
            }

            if($inactif && $compte->isActif()) {
                $compte->setInactif();
                echo sprintf("Le compte %s;%s a été désactivé\n", $compte->_id, $compte->nom);
            }

            if(!$inactif && !$compte->isActif()) {
                $compte->setActif();
                echo sprintf("Le compte %s;%s a été activé\n", $compte->_id, $compte->nom);
            }
            
            if ($compte->isNew()) {
                echo sprintf("Création du compte %s:%s\n", $compte->_id, $compte->nom);
            } elseif ($compte->isModified()) {
                echo sprintf("Modification du compte %s:%s\n", $compte->_id, $compte->nom);
            }
            $compte->save();
            
            if ($compte->getStatut() == "INSCRIT" && !$compte->email) {
                $this->logSection("inscrit ne possédant pas d'email", $compte->get('_id'));
            }
        }

        $tiers_open = array();
        
        foreach($tiers_compte as $id_tiers => $ids_compte) {
            $tiers = sfCouchdbManager::getClient()->retrieveDocumentById($id_tiers, sfCouchdbClient::HYDRATE_DOCUMENT);
            $tiers->remove("compte");
            $tiers->add("compte");

            if(!isset($tiers_open[$tiers->_id])) {
                $tiers->remove("compte");
                $tiers->add("compte");
            }

            foreach($ids_compte as $id_compte) {
                 $tiers->compte->add(null, $id_compte);
            }

            $tiers->save();

            if ($tiers->isModified()) {
                $this->logSection("saved", $tiers->get('_id'));
            }
            $tiers_open[$tiers->_id] = true;
        }
    }

    private function getLogin(array $tiers) {
        $login = null;
        foreach ($tiers as $t) {
            if (($t->type == 'Recoltant' || $t->type == 'Acheteur') && $t->cvi) {
                return $t->cvi;
            }
            if (is_null($login) && $t->civaba) {
                $login = 'C' . $t->civaba;
            }
        }
        return $login;
    }

    private function combiner($tiers_json, $field) {
        $value = null;
        foreach ($tiers_json as $tiers) {
            if ($tiers->type == 'Recoltant' && $tiers->$field) {
                return $tiers->$field;
            } elseif (is_null($value) && $tiers->$field) {
                $value = $tiers->$field;
            }
        }

        return $value;
    }

    private function generatePass() {
        return sprintf("{TEXT}%04d", rand(0, 9999));
    }

}
