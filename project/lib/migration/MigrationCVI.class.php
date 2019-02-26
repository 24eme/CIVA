<?php

class MigrationCVI {

    protected $ancien_cvi = null;
    protected $nouveau_cvi = null;
    protected $keepPassword = true;

    public function __construct($ancien_cvi, $nouveau_cvi, $keepPassword = true) {
        $this->ancien_cvi = $ancien_cvi;
        $this->nouveau_cvi = $nouveau_cvi;
        $this->keepPassword = $keepPassword;
    }

    public function process() {
        if($this->keepPassword) {
            $this->copyPassword();
        }
        $this->createLienSymbolique();
    }

    public function copyPassword() {
        $ancienCompte = CompteClient::getInstance()->find("COMPTE-".$this->ancien_cvi);
        $nouveauCompte = CompteClient::getInstance()->find("COMPTE-".$this->nouveau_cvi);

        if($ancienCompte && $nouveauCompte) {
            $nouveauCompte->mot_de_passe = $ancienCompte->mot_de_passe;
            $nouveauCompte->save();
            echo "Reprise du mot de passe\n";
        }

    }

    public function createLienSymbolique(){
       $drs = DRClient::getInstance()->getAllByCvi($this->ancien_cvi);
       foreach($drs as $dr){
            $nouvelleId = str_replace($dr->cvi, $this->nouveau_cvi, $dr->_id);
            if(DRClient::getInstance()->find($nouvelleId)) {
                echo "Déjà migré ".$dr->_id." => ".$nouvelleId."\n";
                continue;
            }
            $ls = new LS();
            $ls->set('_id', $nouvelleId);
            $ls->setPointeur($dr->_id);
            $ls->save();
            echo "Migration ".$ls->pointeur." => ".$ls->_id."\n";
        }

        $dssIds = DSCivaClient::getInstance()->findAllByCvi($this->ancien_cvi)->getIds();

        foreach($dssIds as $id) {
            $ds = DSCivaClient::getInstance()->find($id, acCouchdbClient::HYDRATE_JSON);
            $nouvelleId = str_replace($ds->identifiant, $this->nouveau_cvi, $id);
            if(DSCivaClient::getInstance()->find($nouvelleId)) {
                echo "Déjà migré ".$id." => ".$nouvelleId."\n";
                continue;
            }
            $ls = new LS();
            $ls->set('_id', $nouvelleId);
            $ls->setPointeur($id);
            echo "Migration ".$ls->pointeur." => ".$ls->_id."\n";
            $ls->save();
        }
    }
}
