<?php

class SV11ProduitForm extends acCouchdbObjectForm
{
    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
    }

    public function configure() {
        $this->setWidget('superficie_recolte', new bsWidgetFormInputFloat(array(), ['placeholder' => 'ares', 'class' => 'form-control text-right input-float', 'disabled' => $this->getObject()->isRebeche()]));
        $this->setValidator('superficie_recolte', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('volume_recolte', new bsWidgetFormInputFloat(array(), array('placeholder' => 'hl', 'class' => 'form-control text-right input-float input_quantite')));
        $this->setValidator('volume_recolte', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('volume_revendique', new bsWidgetFormInputFloat(array(), ['placeholder' => 'hl', 'class' => 'form-control text-right input-float input_volume_revendique', 'disabled' => $this->getObject()->isRebeche()]));
        $this->setValidator('volume_revendique', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('volume_detruit', new bsWidgetFormInputFloat(array(), ['placeholder' => 'hl', 'class' => 'form-control text-right input-float input_volume_revendique', 'disabled' => $this->getObject()->isRebeche()]));
        $this->setValidator('volume_detruit', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('vci', new bsWidgetFormInputFloat(array(), ['placeholder' => 'hl', 'class' => 'form-control text-right input-float input_volume_revendique', 'disabled' => $this->getObject()->isRebeche()]));
        $this->setValidator('vci', new sfValidatorNumber(array('required' => false)));


        $this->widgetSchema->setNameFormat('[%s]');
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
    }
}
