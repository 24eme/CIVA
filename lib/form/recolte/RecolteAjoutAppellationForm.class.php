<?php
    class RecolteAjoutAppellationForm extends sfCouchdbFormDocumentJson {

        protected $_appellation_choices = null;
        protected $_need_lieu = null;

        public function setup() {

          if (!($this->getObject() instanceof DRRecolte)) {
              throw new sfException("Object must be a DRRecolte object");
          }

	  $this->setWidgets(array(
            'appellation' => new sfWidgetFormChoice(array('choices'  => $this->getAppellationChoices())),
          ));
          
          $this->setValidators(array(
            'appellation' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getAppellationChoices()))),
          ));

          $this->widgetSchema->setNameFormat('ajout_apellation[%s]');
          $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
        }

        public function getAppellationChoices() {
            if (is_null($this->_appellation_choices)) {
                $this->_appellation_choices = array('' => '');
                foreach ($this->getObject()->getConfig()->filter('^appellation') as $key => $item) {
                    if (!$this->getObject()->exist($key)) {
                        $this->_appellation_choices[$key] = $item->getLibelle();
                    }
                }
            }

            return $this->_appellation_choices;
        }

        public function doUpdateObject($values) {
            $appellation_key = $values['appellation'];
            $config_appellation = $this->getObject()->getConfig()->get($appellation_key);
            
            $this->getObject()->getCouchdbDocument()->acheteurs->add($appellation_key)->cave_particuliere = 1;

            if ($config_appellation->exist('lieu')) {
                $lieu = $this->getObject()->add($appellation_key)->add('lieu');
		foreach($lieu->getConfig()->filter('^couleur') as $k => $v) {
		  $lieu->add($k);
		}
                $this->_need_lieu = false;
            } else {
                $this->_need_lieu = true;
            }
        }

        public function needLieu() {
          if (is_null($this->_need_lieu)) {
              throw new sfException("Function needLieu must be call after a save()");
          }

          return $this->_need_lieu;
        }

    }

?>