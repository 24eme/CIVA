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
        $this->setValidator("ancien_cvi", new sfValidatorString(array("required" => true)));
        $this->widgetSchema['ancien_cvi']->setAttributes(array('value'=> preg_replace('/COMPTE-/', "", $this->_compte->_id) , 'readonly' => 'true', 'class'=> 'readonly'));
        
        $this->setWidget("nouveau_cvi", new sfWidgetFormInput());
        $this->setValidator("nouveau_cvi", new sfValidatorString(array("required" => true)));
        
        $this->widgetSchema->setNameFormat('new_cvi[%s]');
    }

}


