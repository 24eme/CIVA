<?php

class MigrationEtablissement {

    const PREFIX_KEY_COMPTE= "COMPTE-";
    const PREFIX_KEY_REC= "REC-";
    const PREFIX_KEY_MET = "MET-";
    const PREFIX_KEY_DR = "DR-";
    protected $_ancien_cvi = null;
    protected $_nouveau_cvi = null;
    protected $_ancien_compte = null;
    protected $_nouveau_compte = null;
    protected $_newPassword = false;

    public function __construct($cvi, $nouveau_cvi, $withCopyPasswords = false) {
        $this->_ancien_compte = $compte;
        $this->_ancien_cvi = str_replace(self::PREFIX_KEY_COMPTE, '', $compte->_id);
        $this->_nouveau_cvi = $nouveau_cvi;
        $this->nom = $nom;
        $this->commune = $commune;
        $this->_newPassword = ($withCopyPasswords && $withCopyPasswords=="NON")? sprintf("%4d", rand(0, 9999)) : null;
    }

    public function process(){
        $this->createLienSymbolique();
    }

    public function createNewCompte(){
        $this->_nouveau_compte = clone $this->_ancien_compte;

        $this->_nouveau_compte->date_creation = date('Y-m-d');

        $this->_ancien_compte->setInactif();
        $this->_ancien_compte->update();
        $this->_ancien_compte->save();

        $this->_nouveau_compte->_id = self::PREFIX_KEY_COMPTE . $this->_nouveau_cvi;
        $this->_nouveau_compte->login  =  $this->_nouveau_cvi;
        $this->_nouveau_compte->setActif();
        $this->_nouveau_compte->tiers->remove( self::PREFIX_KEY_REC . $this->_ancien_cvi);
        $this->_nouveau_compte->remove('droits');
        $this->_nouveau_compte->add('droits');
        $this->_nouveau_compte->update();
        $this->_nouveau_compte->save();

        $recoltant = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($this->_ancien_cvi);
        $recoltant->statut = _TiersClient::STATUT_INACTIF;
        $recoltant->save();
        $this->new_rec = clone $recoltant;
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
