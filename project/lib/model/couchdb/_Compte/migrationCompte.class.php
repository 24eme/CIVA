<?php

class MigrationCompte {

    const PREFIX_KEY_COMPTE= "COMPTE-";
    const PREFIX_KEY_REC= "REC-";
    const PREFIX_KEY_MET = "MET-";
    const PREFIX_KEY_DR = "DR-";
    protected $_ancien_cvi = null;
    protected $_nouveau_cvi = null;
    protected $_ancien_compte = null;
    protected $_nouveau_compte = null;

    public function __construct(acCouchdbJson $compte, $nouveau_cvi, $nom = null, $commune = null) {
        $this->_ancien_compte = $compte;
        $this->_ancien_cvi = str_replace(self::PREFIX_KEY_COMPTE, '', $compte->_id);
        $this->_nouveau_cvi = $nouveau_cvi;
        $this->nom = $nom;
        $this->commune = $commune;
    }

    public function process(){
        $this->createNewCompte();
        $this->createCompteTiers();
        $this->createLienSymbolique();

        return ((is_object(acCouchdbManager::getClient('_Compte')->find(self::PREFIX_KEY_COMPTE . $this->_nouveau_cvi))
            &&   is_object(acCouchdbManager::getClient('Recoltant')->retrieveByCvi($this->_nouveau_cvi)))) ? true : false;
    }

    public function createNewCompte(){
        $this->_nouveau_compte = clone $this->_ancien_compte;

        $this->_ancien_compte->mot_de_passe = null;
        $this->_ancien_compte->update();
        $this->_ancien_compte->save();

        $this->_nouveau_compte->_id = self::PREFIX_KEY_COMPTE . $this->_nouveau_cvi;
        $this->_nouveau_compte->login  =  $this->_nouveau_cvi;
        $this->_nouveau_compte->update();
        $this->_nouveau_compte->save();

        $id_recoltant = self::PREFIX_KEY_REC . $this->_ancien_cvi;
        $this->_nouveau_compte->tiers->add(self::PREFIX_KEY_REC . $this->_nouveau_cvi, $this->_nouveau_compte->tiers->get($id_recoltant));
        $this->_nouveau_compte->tiers->remove($id_recoltant);
        $this->_nouveau_compte->tiers->get(self::PREFIX_KEY_REC . $this->_nouveau_cvi)->set('id', self::PREFIX_KEY_REC . $this->_nouveau_cvi );
        if(!is_null($this->nom))
            $this->_nouveau_compte->tiers->get(self::PREFIX_KEY_REC . $this->_nouveau_cvi)->set('nom', $this->nom);

        $this->_nouveau_compte->update();
        $this->_nouveau_compte->save();

        $recoltant = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($this->_ancien_cvi);
        $recoltant->statut = _TiersClient::STATUT_INACTIF;
        $recoltant->save();
        $this->new_rec = clone $recoltant;
    }

    public function createCompteTiers(){
        $this->new_rec->_id = self::PREFIX_KEY_REC . $this->_nouveau_cvi;
        $this->new_rec->compte = array(self::PREFIX_KEY_COMPTE . $this->_nouveau_cvi);

        if(!is_null($this->nom))
            $this->new_rec->nom = $this->nom;

        if(!is_null($this->commune))
            $this->new_rec->commune= $this->commune;

        $this->new_rec->update();
        $this->new_rec->save();
    }

    public function createLienSymbolique(){

       $drs = acCouchdbManager::getClient('DR')->getAllByCvi($this->_ancien_cvi);
       foreach($drs as $dr){
            $ls_dr = new LS();
            $ls_dr->set('_id', self::PREFIX_KEY_DR . $this->_nouveau_cvi . '-' . $dr->campagne);
            $ls_dr->setPointeur($dr->_id);
            $ls_dr->update();
            $ls_dr->save();
        }
    }
}