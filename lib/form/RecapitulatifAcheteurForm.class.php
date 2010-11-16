<?php
    class RecapitulatifAcheteurForm extends sfCouchdbFormDocumentJson  {

        public function configure() {
	  $this->setWidgets(array(
				  'superficie' => new sfWidgetFormInputFloat(array()),
				  'dontdplc' => new sfWidgetFormInputFloat(array()),
				  )
			    );
            $this->setValidators(array(
                'superficie' => new sfValidatorNumber(array('required' => false)),
                'dontdplc' => new sfValidatorNumber(array('required' => false)),
            ));
            
            $this->widgetSchema->setNameFormat('acheteur[%s]');
        }
    }

?>