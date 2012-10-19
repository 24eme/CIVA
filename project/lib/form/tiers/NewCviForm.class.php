<?php

class NewCviForm extends BaseForm {
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
    

    protected function checkCompte() {
        if (!$this->_compte) {
            throw new sfException("compte does exist");
        }
    }
    
    public function configure() {
        $this->setWidget("ancien_cvi", new sfWidgetFormInput());
        $this->widgetSchema['ancien_cvi']->setAttributes(array('value'=> preg_replace('/COMPTE-/', "", $this->_compte->_id) , 'readonly' => 'true', 'class'=> 'readonly'));
        $this->setValidator("ancien_cvi", new sfValidatorString(array("required" => true)));
        $this->getValidator("ancien_cvi")->setMessage("required", "Champs obligatoire");


        $this->setWidget("nouveau_cvi", new sfWidgetFormInput());
        $this->setValidator("nouveau_cvi", new ValidatorNewCvi(array("required" => true, "min" => 0000000001 , "max" => 9999999999)));
        $this->getValidator("nouveau_cvi")->setMessage("invalid", "Le cvi doit être composé de 10 digits");

        $this->widgetSchema->setNameFormat('new_cvi[%s]');
    }

}


