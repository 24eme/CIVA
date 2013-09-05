<?php

class RecapitulatifForm extends acCouchdbObjectForm {

    protected $is_saisisable = false;

    public function __construct(acCouchdbJson $object, $options = array(), $CSRFSecret = null) {
        parent::__construct($object, $options, $CSRFSecret);
        $this->getDocable()->remove();
    }

    public function configure() {
        $lieu = $this->getObject();
        if($lieu->canHaveUsagesLiesSaisi() && $lieu->getConfig()->existRendement() && !$lieu->getConfig()->existRendementCouleur()){
            $this->setWidgets(array(
                'lies' => new sfWidgetFormInputFloat(array()),
            ));

            $this->setValidators(array(
                'lies' => new sfValidatorNumber(array('required' => false)),
            ));

            $this->getWidget('lies')->setLabel('Usages industriels saisis');

            $this->is_saisisable = true;
        }

        if($this->getObject()->getConfig()->existRendementCouleur()) {
            foreach($this->getObject()->getCouleurs() as $couleur) {
                $this->embedForm($couleur->getKey(), new RecapitulatifCouleurUsagesIndustrielsForm());            
            }
            $this->is_saisisable = true;
        }
        
        $form_acheteurs = new BaseForm();
        foreach ($lieu->acheteurs as $type => $acheteurs_type) {
            $form_type = new BaseForm();
            foreach ($acheteurs_type as $cvi => $acheteur) {
                $form_type->embedForm($cvi, new RecapitulatifAcheteurForm());
                
                $this->is_saisisable = true;
            }
            $form_acheteurs->embedForm($type, $form_type);
        }
        $this->embedForm('acheteurs', $form_acheteurs);

        
        $this->getValidatorSchema()->setPostValidator(new ValidatorRecapitulatif(null, array('object' => $this->getObject())));
        $this->widgetSchema->setNameFormat('recapitulatif[%s]');

        $this->disableLocalCSRFProtection();
        $this->disabledRevisionVerification();
    }

    public function isSaisisable() {

        return $this->is_saisisable;
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);

        $this->getObject()->getCouchdbDocument()->update();
    }
}

?>