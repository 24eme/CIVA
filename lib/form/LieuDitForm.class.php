<?php
    class LieuDitForm extends sfCouchdbFormDocumentJson {
        public function setup() {
          $lieu_choices = $this->getObject()->getLieuChoices();

	  $this->setWidgets(array(
				    'lieu' => new sfWidgetFormChoice(array(
									   'choices'  => $lieu_choices,
									   )
								     )
				    )
			    );
            $this->setValidators(array(
                'lieu' => new sfValidatorChoice(array('required' => $this->getOption('lieu_required', true), 'choices' => array_keys($lieu_choices))),
            ));
            
            $this->widgetSchema->setNameFormat('lieudit[%s]');
            
            $this->validatorSchema['lieu']->setMessage('required', 'Champ obligatoire');
        }

        public function doUpdateObject($values) {
            if (isset($values['lieu'])) {
                $this->getObject()->add($values['lieu']);
            }
        }
    }

?>