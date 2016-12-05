<?php
abstract class _Compte extends Base_Compte {
    const STATUS_NOUVEAU = CompteClient::STATUT_TELEDECLARANT_NOUVEAU;
    const STATUS_INSCRIT = CompteClient::STATUT_TELEDECLARANT_INSCRIT;
    const STATUS_INACTIF = CompteClient::STATUT_TELEDECLARANT_INACTIF;
    const STATUS_MOT_DE_PASSE_OUBLIE = CompteClient::STATUT_TELEDECLARANT_OUBLIE;

    public function constructId() {
        parent::constructId();
        $this->set('_id', 'COMPTE-' . $this->login);
        $this->date_creation = date("Y-m-d");
        if(!$this->statut) {
            $this->statut = self::STATUS_NOUVEAU;
        }
        if($this->statut == self::STATUS_NOUVEAU && !$this->mot_de_passe) {
            $this->mot_de_passe = $this->generatePass();
        }
    }

    public function getEtablissementOrigineObject() {

        return null;
    }

    /**
     *
     * @param string $mot_de_passe
     */
    public function setMotDePasseSSHA($mot_de_passe) {
        mt_srand((double) microtime() * 1000000);
        $salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
        $hash = "{SSHA}" . base64_encode(pack("H*", sha1($mot_de_passe . $salt)) . $salt);
        $this->_set('mot_de_passe', $hash);
    }

    /**
     *
     * @param string $mot_de_passe
     */
    public function setPasswordSSHA($mot_de_passe) {
        mt_srand((double)microtime()*1000000);
        $salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
        $hash = "{SSHA}" . base64_encode(pack("H*", sha1($mot_de_passe . $salt)) . $salt);
        $this->_set('mot_de_passe', $hash);
    }

    public function getCodeCreation() {
        if($this->statut != self::STATUS_NOUVEAU) {
            return null;
        }

        return preg_replace('/^\{TEXT\}/', "", $this->mot_de_passe);
    }

    /**
     *
     */
    protected function updateStatut() {
        if(!$this->isActif()) {
            return;
        }

        if (substr($this->mot_de_passe,0,6) == '{SSHA}') {
           $this->_set('statut', self::STATUS_INSCRIT);
        } elseif(substr($this->mot_de_passe,0,6) == '{TEXT}') {
           $this->_set('statut', self::STATUS_NOUVEAU);
        } elseif(substr($this->mot_de_passe,0,8) == '{OUBLIE}') {
           $this->_set('statut', self::STATUS_MOT_DE_PASSE_OUBLIE);
        } else {
           $this->_set('statut', self::STATUS_INACTIF);
        }
    }

    public function getStatus() {
        $this->updateStatut();

        return $this->statut;
    }

    /**
     *
     * @return string
     */
    public function getNom() {
        return ' ';
    }

    /**
     *
     * @return string
     */
    public function getIntitule() {
        return '';
    }

    /**
     *
     * @return string
     */
    public function getGecos() {
        return ',,'.$this->getNom().',';
    }

    /**
     *
     * @return string
     */
    public function getAdresse() {
        return ' ';
    }

    /**
     *
     * @return string
     */
    public function getCodePostal() {
        return ' ';
    }

    /**
     *
     * @return string
     */
    public function getCommune() {
        return  ' ';
    }

    /**
     *
     */
    public function updateLdap() {
        $ldap = new Ldap();

        if($ldap->exist($this)) {
            $ldap->update($this);

            return;
        }

        if (!$ldap->exist($this) && $this->statut == self::STATUS_INSCRIT) {
            $ldap = new Ldap();
            $ldap->add($this);

            return;
        }
    }

    public function isOperateur() {

        return $this->exist('droits') && (in_array(_CompteClient::DROIT_OPERATEUR, $this->droits->toArray(true, false)) || in_array(_CompteClient::DROIT_OPERATEUR, $this->droits->toArray(true, false)));
    }

    public function isActif() {

        return $this->statut != self::STATUS_INACTIF;
    }

    public function isInscrit()
    {
    	return $this->statut != self::STATUS_NOUVEAU && $this->isActif();
    }

    public function setInactif() {
        $this->statut = self::STATUS_INACTIF;
    }

    public function setActif() {
        $this->statut = self::STATUS_INSCRIT;
        if(!$this->mot_de_passe) {
            $this->mot_de_passe = $this->generatePass();
        }
        $this->updateStatut();
    }

    public function getComptesPersonnes() {

        return _CompteClient::getInstance()->getComptesPersonnes($this);
    }

    public function save() {
        $this->updateStatut();
        $this->updateLdap();
        parent::save();
    }

    public function resetMotDePasseFromLdap() {
        $ldap = new Ldap();
        $info = $ldap->get($this);
        if($info && is_array($info['userpassword']) && count($info['userpassword']) > 0 && $info['userpassword'][0]) {
            $this->mot_de_passe = $info['userpassword'][0];
        }
    }

    public function hasDelegation(){
        return false;
    }

    public function generatePass() {
        return sprintf("{TEXT}%04d", rand(1000, 9999));
    }


}
