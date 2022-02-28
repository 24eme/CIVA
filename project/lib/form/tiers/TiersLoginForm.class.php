<?php

class TiersLoginForm extends BaseForm {
    protected $_compte = null;

    /**
     *
     * @param Compte $compte
     * @param array $options
     * @param string $CSRFSecret
     */
    public function __construct(Compte $compte, $options = array(), $CSRFSecret = null) {
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
        $this->setWidget("tiers", new sfWidgetFormChoice(array("expanded" => true, "choices" => $this->getChoiceTiers())));
        $this->setValidator("tiers", new sfValidatorChoice(array("choices" => array_keys($this->getChoiceTiers()),
                                                                 "required" => true)));

        $this->getValidator("tiers")->setMessage("required", "Champs obligatoire");

        $this->widgetSchema->setNameFormat('tiers[%s]');
    }

    public function process() {
        if ($this->isValid()) {
            return acCouchdbManager::getClient()->find($this->getValue('tiers'));
        } else {
            throw new sfException("must be valid");
        }
    }

    public function getChoiceTiers() {
        $choices = array();
        foreach($this->_compte->getSociete()->getEtablissementsObject(true, true) as $id => $item) {
            $choices[$id] = $item->nom . ' - ' . $item->getFamille();
        }

        return $choices;
    }
}
