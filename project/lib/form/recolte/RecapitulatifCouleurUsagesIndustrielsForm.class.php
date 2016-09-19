<?php
    class RecapitulatifCouleurUsagesIndustrielsForm extends BaseForm     {

        public function configure() {
	       $this->setWidgets(array(
                'lies' => new sfWidgetFormInputFloat(array()),
            ));

            $this->setValidators(array(
                'lies' => new sfValidatorNumber(array('required' => false)),
            ));

            $this->getWidget('lies')->setLabel('Volume à détruire saisi');

            $this->widgetSchema->setNameFormat('acheteur[%s]');
        }
    }
