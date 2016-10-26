<?php
    class RecolteAjoutAppellationForm extends acCouchdbObjectForm {

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

        protected function updateDefaultsFromObject() {

        }

        public function getAppellationChoices() {
            if (is_null($this->_appellation_choices)) {
                $this->_appellation_choices = array();
                $appellations = $this->getObject()->getDocument()->getAppellationsAvecVtsgn();
                foreach ($this->getObject()->getDocument()->getConfigAppellationsAvecVtsgn() as $key => $item) {
                    if(array_key_exists(str_replace("/mention", "", $item["hash"]), $appellations)) {
                        continue;
                    }
                    $this->_appellation_choices[$key] = $item["libelle"];
                }
            }

            return $this->_appellation_choices;
        }

        public function doUpdateObject($values) {
            $appellation_key = $values['appellation'];
            $appellationsConfig = $this->getObject()->getDocument()->getConfigAppellationsAvecVtsgn();
            $this->getObject()->getDocument()->acheteurs->getNoeudAppellations()->add($appellation_key)->cave_particuliere = 1;
            $this->getObject()->getDocument()->update(array('from_acheteurs'));
            $this->values['appellation_hash'] = $appellationsConfig[$appellation_key]["hash"];

            $this->_need_lieu = false;
            if($this->getObject()->getDocument()->exist($this->values['appellation_hash'])) {
                $this->_need_lieu = $this->getObject()->getDocument()->get($this->values['appellation_hash'])->getConfig()->hasManyLieu();
            }
            /*if ($config_appellation->mention->exist('lieu')) {
                $lieu = $this->getObject()->getNoeudAppellations()->add($appellation_key)->mention->add('lieu');
                foreach($lieu->getConfig()->filter('^couleur') as $k => $v) {
                  $lieu->add($k);
                }
                $this->_need_lieu = false;
            } else {
                $this->_need_lieu = true;
            }*/
        }

        public function needLieu() {
          if (is_null($this->_need_lieu)) {
              throw new sfException("Function needLieu must be call after a save()");
          }

          return $this->_need_lieu;
        }

    }
