<?php

/**
 * Description of ExportDRAcheteursCsv
 *
 * @author vince
 */
class ExportUserDRValideeCsv extends ExportCsv {

    protected $_headers = array(
        "cvi_acheteur" => "CVI acheteur",
        "nom_acheteur" => "nom acheteur",
        "cvi_recoltant" => "CVI récoltant",
        "nom_recoltant" => "nom récoltant",
        "creation_date" => "date de création",
        "validation_date" => "date de validation",
        "validation_user" => "validateur",
    );

    protected $_acheteur = null;
    protected $_debug = false;
    protected $_ids_dr = null;
    protected $_campagne = null;

    /**
     *
     * @param string $campagne 
     */
    public function __construct($campagne, $acheteur_or_cvi, $debug = false) {
        parent::__construct($this->_headers);
        $this->_debug = $debug;
        $this->_campagne = $campagne;
        if ($acheteur_or_cvi instanceof Acheteur) {
            $this->_acheteur = $acheteur_or_cvi;
        } else {
            $this->_acheteur = sfCouchdbManager::getClient("Acheteur")->retrieveByCvi($acheteur_or_cvi);
        }
        if (!$this->_acheteur) {
            throw new sfException("Acheteur not find");
        }
        $drs = sfCouchdbManager::getClient("DR")->findAllByCampagneAndCviAcheteur($this->_campagne, $this->_acheteur->cvi, sfCouchdbClient::HYDRATE_JSON);

        $this->_ids_dr = $drs->getIds();
    }
    
    public function add($data, $validation = array()) {
        $line = parent::add($data, $validation);
        if ($this->_debug) {
            echo $line;
        }
        return $line;
    }

    public function export() {
        foreach ($this->_ids_dr as $id_dr) {
            $dr = sfCouchdbManager::getClient()->retrieveDocumentById($id_dr);
            if(!is_null($dr->validee) )
            {
                $this->dr = $dr;
                if (substr($dr->cvi, 0, 1) == "6") {
                    if ($this->_debug) {
                        echo "\n\n ------------ \n" . $dr->get('_id') . "\n ----------- \n";
                    }
                    $this->addUtilisateurs($dr);
                }unset($dr);
            }
        }
        if ($this->_debug) {
            echo "------------ \n" . count($this->_ids_dr) . " DRs \n ------------\n";
        }
    }

    protected function addUtilisateurs(DR $dr) {
        $this->add(array(
            "cvi_acheteur" => $this->_acheteur->cvi,
            "nom_acheteur" => $this->_acheteur->nom,
            "cvi_recoltant" => $dr->cvi,
            "nom_recoltant" => $dr->declarant->nom,
            "creation_date" => $this->dr->getPremiereModificationDr(),
            "validation_date" => $dr->validee,
            "validation_user" => $this->getValidationUser($dr),
        ), array());
    }
    
    private function getValidationUser($dr) {
        $user = null;
        if ($dr->exist('utilisateurs')) {
            foreach($dr->utilisateurs->validation as $compte => $date_fr) {
                if (preg_match('/^COMPTE-[0-9]+$/', $compte)) {
                    $user = "Récoltant";
                } elseif(preg_match('/^COMPTE-.*civa.*$/', $compte)) {
                    $user = "CIVA";
                } elseif(!preg_match('/^COMPTE-/', $compte)) {
                    $user = $compte;
                }
            }
        }
        if (!$user && strtotime($dr->validee) > strtotime($this->_campagne.'-12-10')) {
            $user = 'Automatique';
        } elseif(!$user) {
            $user = 'Récoltant';
        }

        return $user;
    }



}