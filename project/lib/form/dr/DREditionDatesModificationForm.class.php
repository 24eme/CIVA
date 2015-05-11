<?php

class DREditionDatesModificationForm extends acCouchdbForm 
{
    protected $ds = null;
    protected $user = null;
    protected $compteAdmin = null;
    protected $compteUser = null;
    
    public function __construct(DR $dr, $user, $options = array(), $CSRFSecret = null) 
    {
        $this->dr = $dr;
        $this->user = $user;
        $this->compteUserAuthenticated = $user->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED);
        $this->compteUser = $user->getCompte();
        
        $defaults = array();
        if($this->dr->isValideeTiers()) {
            $defaults['date'] = ($this->dr->exist('validee') && $this->dr->validee) ? $this->dr->getDateValideeFr() : date('d/m/Y');
        } else {
            $defaults['date'] = date('d/m/Y');
        }        
        parent::__construct($dr, $defaults, $options, $CSRFSecret);
    }
    
    public function configure() 
    {
        $this->setWidgets(array(
               'date' => new sfWidgetFormInput(),
               'compte' =>  new sfWidgetFormChoice(array('choices' => $this->getChoices()))
        ));
        $this->widgetSchema->setLabels(array(
            'date' => (!$this->dr->isValideeTiers()) ? "Validée le" : "Modifiée le",
            'compte' => 'Modifiee par '
        ));
        $this->setValidators(array(
                'date' => new sfValidatorRegex(array('required' => true, 'pattern' => "/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/"),array('invalid' => 'Le format de la date d\'édition doit être jj/mm/aaaa')),
                'compte' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getChoices())),array('required' => "L'utilisateur modificateur est obligatoire"))
            ));

        if(!$this->dr->isValideeTiers()) {
            unset($this['compte']);
        }

        if($this->dr->hasDateDepotMairie()) {
            unset($this['compte']);
        }
        
        $this->widgetSchema->setNameFormat('dr_validation[%s]');
    }
    
    public function getChoices()
    {
       return array($this->compteUserAuthenticated->get('_id') => $this->compteUserAuthenticated->get('_id'),
                    $this->compteUser->get('_id') => $this->compteUser->get('_id'));
    }

    public function getDate()
    {
        
        return Date::getIsoDateFromFrenchDate($this->values['date']);
    }

    public function getCompteId() {

        if(!isset($this->values['compte'])) {
            
            return false;
        }

        return $this->values['compte'];
    }
}