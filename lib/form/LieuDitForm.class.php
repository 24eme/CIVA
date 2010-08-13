<?php
    class LieuDitForm extends BaseForm {
        public function setup() {
	  $lieux = array('' => '');
	  foreach (ConfigurationClient::getConfiguration()->get('/recolte/appellation_GRDCRU')->filter('lieu[0-9]') as $k => $l) {
	    $lieux[$k] = $l->getLibelle();
	  }
          asort($lieux);
	  $this->setWidgets(array(
				    'lieu' => new sfWidgetFormChoice(array(
									   'choices'  => $lieux,
									   )
								     )
				    )
			    );
            $this->setValidators(array(
                'lieu' => new sfValidatorString(array('required' => true)),
            ));
            
            $this->widgetSchema->setNameFormat('lieudit[%s]');
        }
    }

?>