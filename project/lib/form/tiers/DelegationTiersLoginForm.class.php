<?php

class DelegationTiersLoginForm extends BaseForm {
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
        $this->setWidget("tiers", new sfWidgetFormChoice(array("choices" => $this->getChoiceDelegationTiers())));
        $this->setValidator("tiers", new sfValidatorChoice(array("choices" => array_keys($this->getChoiceDelegationTiers()),
                                                                 "required" => true)));
        
        $this->getValidator("tiers")->setMessage("required", "Champs obligatoire");
        
        $this->widgetSchema->setNameFormat('delegation[%s]');
    }

    /**
     *
     * @return _Tiers;
     */
    public function process() {
        if ($this->isValid()) {
            return sfCouchdbManager::getClient()->retrieveDocumentById('COMPTE-'.$this->getValue('tiers'));
        } else {
            throw new sfException("must be valid");
        }
    }
    
    public function getChoiceDelegationTiers() {
        $choices = array();
        foreach($this->_compte->getTiersDelegation() as $id => $item) {

            $choices[$item->id] = $item->nom . ' - ' . $item->type;
        }
        return $choices;
    }
}


