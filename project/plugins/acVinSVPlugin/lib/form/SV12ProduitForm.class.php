<?php

class SV12ProduitForm extends acCouchdbObjectForm
{
    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $this->setDefault('coefficient', $this->getObject()->getCoefficient());
    }

    public function configure() {
        $this->setWidget('superficie_recolte', new bsWidgetFormInputFloat(array(), array('placeholder' => 'ares', 'class' => 'form-control text-right input-float input-sm')));
        $this->setValidator('superficie_recolte', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('quantite_recolte', new bsWidgetFormInputInteger(array(), array('placeholder' => 'kg', 'class' => 'form-control text-right input-integer input-sm input_quantite')));
        $this->setValidator('quantite_recolte', new sfValidatorInteger(array('required' => false)));

        $this->setWidget('coefficient', new bsWidgetFormInputFloat(array(), array('class' => 'form-control text-right input-float input-sm input_coefficient')));
        $this->setValidator('coefficient', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('volume_recolte', new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input-sm')));
        $this->setValidator('volume_recolte', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('volume_revendique', new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input-sm input_volume_revendique')));
        $this->setValidator('volume_revendique', new sfValidatorNumber(array('required' => false)));

        $this->widgetSchema->setNameFormat('[%s]');
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
    }
}
