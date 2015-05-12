<?php

class DSEditionDatesModificationFormCiva extends acCouchdbForm 
{
    protected $ds = null;
    protected $user = null;
    protected $compteAdmin = null;
    protected $compteUser = null;
    
    public function __construct(DSCiva $ds, $user, $options = array(), $CSRFSecret = null) 
    {
        $this->ds = $ds;
        $this->user = $user;
        $this->compteAdmin = $user->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED);
        $this->compteUser = $user->getCompte();
        
        $defaults = array();
        if(!$ds->isDsPrincipale()){
            throw new sfException("La Ds utilisée pour l'attribution du compte modificateur ne peut être que la DS principale.");
        }
        $defaults['date_edition'] = $this->ds->getDateEditionFr();
        $defaults['date_validation'] = $this->ds->getDateValidationFr();
        $defaults['utilisateurs'] = $this->compteAdmin->get('_id');
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }
    
    public function configure() 
    {
        $this->setWidgets(array(
               'date_edition' => new sfWidgetFormInput(),
               'date_validation' => new sfWidgetFormInput(),
               'utilisateurs' =>  new sfWidgetFormChoice(array('choices' => $this->getChoices()))
    	));
        $this->widgetSchema->setLabels(array(
        	'date_edition' => 'Date de modification *:',
                'date_validation' => 'Date de validation *:',
        	'utilisateurs' => 'Utilisateur *:'
        ));
        $this->setValidators(array(
                'date_edition' => new sfValidatorRegex(array('required' => true, 'pattern' => "/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/"),array('invalid' => 'Le format de la date d\'édition doit être jj/mm/aaaa')),
                'date_validation' => new sfValidatorRegex(array('required' => true, 'pattern' => "/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/"),array('invalid' => 'Le format de la date de validation doit être jj/mm/aaaa')),
                'utilisateurs' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getChoices())),array('required' => "Aucun lieu n'a été choisi !"))
            ));
        
        $this->widgetSchema->setNameFormat('ds_edit_dates[%s]');
    }
    
    public function getChoices()
    {
       return array($this->compteAdmin->get('_id') => $this->compteAdmin->get('_id'),
                    $this->compteUser->get('_id') => $this->compteUser->get('_id'));
    }
    
    public function doUpdateDatesModificationValidation($dates_utilisateurs,$ds)
    {
        $date_edition = Date::getIsoDateFromFrenchDate($dates_utilisateurs['date_edition']);
        $date_validation =  Date::getIsoDateFromFrenchDate($dates_utilisateurs['date_validation']);
                        
        $ds->addEdition($dates_utilisateurs['utilisateurs'],$date_edition);
        $ds->addValidation($dates_utilisateurs['utilisateurs'],$date_validation);
        $ds->validee = $date_validation;
        $ds->modifiee = $date_edition;
        $ds->save();
        return $ds;
    }
}