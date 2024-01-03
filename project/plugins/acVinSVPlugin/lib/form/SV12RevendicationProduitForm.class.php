<?php

class SV12RevendicationProduitForm extends acCouchdbObjectForm
{
    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        if($this->getObject()->exist('volume_mouts')) {
            $this->setDefault('volume_mouts_revendique', $this->getObject()->volume_mouts);
        }
    }

    public function configure()
    {
        if ($this->getObject()->superficie_recolte || $this->getObject()->quantite_recolte || $this->getObject()->volume_revendique) {
            $this->setWidget('volume_revendique', new bsWidgetFormInputFloat(array(), ['placeholder' => '', 'class' => 'form-control text-right input-float input_volume_revendique']));
            $this->setValidator('volume_revendique', new sfValidatorNumber(array('required' => false)));
        }

        if($this->getObject()->isRebeche()) {
            $this->setWidget('volume_revendique', new bsWidgetFormInputFloat(array(), ['placeholder' => '', 'class' => 'form-control text-right input-float input_volume_revendique', 'readonly' => 'readonly']));
        }

        if($this->getObject()->exist('volume_mouts')) {
            $this->setWidget('volume_mouts_revendique', new bsWidgetFormInputFloat(array(), array('class' => 'form-control text-right input-float input_volume_revendique', 'readonly' => 'readonly')));
            $this->setValidator('volume_mouts_revendique', new sfValidatorNumber(array('required' => false)));
        }

        $this->widgetSchema->setNameFormat('[%s]');
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
    }
}
