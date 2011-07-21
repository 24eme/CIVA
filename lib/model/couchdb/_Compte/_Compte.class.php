<?php
abstract class _Compte extends Base_Compte {
    const STATUS_NOUVEAU = 'NOUVEAU';
    const STATUS_INSCRIT = 'INSCRIT';
    const STATUS_MOT_DE_PASSE_OUBLIE = 'MOT_DE_PASSE_OUBLIE';
    
    public function setPasswordSSHA($password) {
        throw new sfException("not defined");
    }
    
    protected function updateStatut() {
       if (substr($this->mot_de_passe,0,6) == '{SSHA}') {
           $this->_set('statut') = self::STATUS_INSCRIT;
       } elseif(substr($this->mot_de_passe,0,6) == '{TEXT}') {
           $this->_set('statut') = self::STATUS_NOUVEAU;
       } elseif(substr($this->mot_de_passe,0,8) == '{OUBLIE}') {
           $this->_set('statut') = self::STATUS_MOT_DE_PASSE_OUBLIE;
       } else {
           $this->_set('statut') = null;
       }
    }
    
    public function getStatus() {
        $this->updateStatus();
        return $this->_get('status');
    }
    
    public function setStatus() {
        throw new sfException("Compte status is not editable");
    }
    
    protected function updateLdap() {
        $ldap = new Ldap();
        if($ldap->exist($this)) {
            $ldap->update($this);
        }else {
            $ldap->add($this);
        }
    }
    
    public function save() {
        $this->updateStatus();
        $this->updateLdap();
        parent::save();
    }
    
}
