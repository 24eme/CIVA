<?php

class DelegationLoginForm extends BaseForm {
    protected $_compte = null;
    
    /**
     *
     * @param Compte $compte
     * @param array $options
     * @param string $CSRFSecret 
     */
    public function __construct(_Compte $compte, $options = array(), $CSRFSecret = null) {
        $this->_compte = $compte;
        $this->checkCompte();
        parent::__construct(array(), $options, $CSRFSecret);
    }
    
    /**
     * 
     */
    protected function checkCompte() {
        if (!$this->_compte) {
            throw new sfException("compte does exist");
        }
    }
    
    public function configure() {
        $this->setWidget("compte", new sfWidgetFormInputText());
        $this->setValidator("compte", new sfValidatorChoice(array("choices" => array_keys($this->getChoiceDelegation()),
                                                                 "required" => true)));
        
        $this->getValidator("compte")->setMessage("required", "Champs obligatoire");
        $this->getValidator("compte")->setMessage("invalid", "Ce compte n'existe pas ou vous n'avez pas les droits de délégation pour celui-ci");
        
        $this->widgetSchema->setNameFormat('delegation[%s]');
    }

    /**
     *
     * @return _Tiers;
     */
    public function process() {
        if ($this->isValid()) {
            return sfCouchdbManager::getClient()->retrieveDocumentById('COMPTE-'.$this->getValue('compte'));
        } else {
            throw new sfException("must be valid");
        }
    }
    
    public function getChoiceDelegation() {
        $choices = array();
        foreach($this->_compte->delegation as $id => $nom) {

            $choices[str_replace('COMPTE-', '', $id)] = $nom;
        }
        return $choices;
    }
}


