<?php
    class RecapitulatifCouleurUsagesIndustrielsForm extends BaseForm     {

        public function configure() {
	       $this->setWidgets(array(
                'usages_industriels' => new sfWidgetFormInputFloat(array()),
            ));

            $this->setValidators(array(
                'usages_industriels' => new sfValidatorNumber(array('required' => false)),
            ));

            $this->getWidget('usages_industriels')->setLabel('Usages industriels');
            $this->getValidator('usages_industriels')->setMessage('max', "Les usages industriels ne peuvent pas être supérieurs au volume total récolté");
            
            $this->widgetSchema->setNameFormat('acheteur[%s]');
        }
    }

?>