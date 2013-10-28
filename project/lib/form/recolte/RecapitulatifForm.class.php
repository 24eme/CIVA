<?php

class RecapitulatifForm extends acCouchdbObjectForm {

    protected $is_lies_saisisables = false;

    public function __construct(acCouchdbJson $object, $options = array(), $CSRFSecret = null) {
        parent::__construct($object, $options, $CSRFSecret);
    }

    public function configure() {
        $object = $this->getObject();
        $this->is_lies_saisisable = false;
        if($object->canHaveUsagesLiesSaisi() && $object->getConfig()->existRendement() && $this->getObject()->getTotalCaveParticuliere() > 0){
            $this->setWidgets(array(
                'lies' => new sfWidgetFormInputFloat(array()),
            ));

            $this->setValidators(array(
                'lies' => new sfValidatorNumber(array('required' => false)),
            ));

            $this->getWidget('lies')->setLabel('Usages industriels saisis');

            $this->is_lies_saisisables = true;
        }
        
        $form_acheteurs = new BaseForm();
        foreach ($object->acheteurs as $type => $acheteurs_type) {
            $form_type = new BaseForm();
            foreach ($acheteurs_type as $cvi => $acheteur) {
                $form_type->embedForm($cvi, new RecapitulatifAcheteurForm($acheteur));
            }
            $form_acheteurs->embedForm($type, $form_type);
        }

        $this->getValidatorSchema()->setPostValidator(new ValidatorRecapitulatif(null, array('object' => $this->getObject())));

        $this->embedForm('acheteurs', $form_acheteurs);
    }

    public function doUpdateObject($values) {

        return parent::doUpdateObject($values);
    }

    public function isLiesSaisisables() {

        return $this->is_lies_saisisables;
    }
}