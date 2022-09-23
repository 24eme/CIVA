<?php

class SV11ProduitForm extends acCouchdbObjectForm
{
    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
    }

    public function configure() {
        $this->setWidget('superficie_recolte', new bsWidgetFormInputFloat(array(), array('placeholder' => 'ares', 'class' => 'form-control text-right input-float input-sm')));
        $this->setValidator('superficie_recolte', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('volume_recolte', new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input-sm input_quantite')));
        $this->setValidator('volume_recolte', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('volume_revendique', new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input-sm input_volume_revendique')));
        $this->setValidator('volume_revendique', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('usages_industriels', new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input-sm input_volume_revendique')));
        $this->setValidator('usages_industriels', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('vci', new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input-sm input_volume_revendique')));
        $this->setValidator('vci', new sfValidatorNumber(array('required' => false)));


        $this->widgetSchema->setNameFormat('[%s]');
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
    }
}
