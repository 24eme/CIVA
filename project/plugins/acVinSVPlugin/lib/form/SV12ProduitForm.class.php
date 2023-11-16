<?php

class SV12ProduitForm extends acCouchdbObjectForm
{
    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $this->setDefault('taux_extraction', $this->getObject()->getTauxExtraction());
    }

    public function configure() {
        $this->setWidget('superficie_recolte', new bsWidgetFormInputFloat(array(), ['class' => 'form-control text-right input-float', 'disabled' => $this->getObject()->isRebeche(), 'tabindex' => -1]));
        $this->setValidator('superficie_recolte', new sfValidatorNumber(array('required' => false)));

        $this->setWidget('quantite_recolte', new bsWidgetFormInputInteger(array(), array('class' => 'form-control text-right input-integer input_quantite', 'disabled' => $this->getObject()->isRebeche())));
        $this->setValidator('quantite_recolte', new sfValidatorInteger(array('required' => false)));

        $this->setWidget('taux_extraction', new bsWidgetFormInputFloat(array(), array('class' => 'form-control text-right input-float input_taux_extraction', 'readonly' => 'readonly', 'tabindex' => -1)));
        $this->setValidator('taux_extraction', new sfValidatorNumber(array('required' => false)));

        if($this->getObject()->exist('volume_mouts')) {
            $this->setWidget('volume_mouts', new bsWidgetFormInputFloat(array(), array('class' => 'form-control text-right input-float input_volume_revendique')));
            $this->setValidator('volume_mouts', new sfValidatorNumber(array('required' => false)));

            $this->setWidget('superficie_mouts', new bsWidgetFormInputFloat(array(), array('class' => 'form-control text-right input-float')));
            $this->setValidator('superficie_mouts', new sfValidatorNumber(array('required' => false)));
        }

        $this->widgetSchema->setNameFormat('[%s]');
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
    }
}
