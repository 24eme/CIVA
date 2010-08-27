<?php
    class RecolteAjoutLieuForm extends sfCouchdbFormDocumentJson {
        
        protected $_lieu_choices = null;
        
        public function setup() {

          if (!($this->getObject() instanceof DRRecolteAppellation)) {
              throw new sfException("Object must be a DRRecolteAppellation object");
          }

          if (!$this->getObject()->hasManyLieu()) {
              throw new sfException("This appellation can't have lieu");
          }

	  $this->setWidgets(array(
            'appellation' => new sfWidgetFormChoice(array('choices'  => array($this->getObject()->getKey() => $this->getObject()->getConfig()->getLibelle())), array('disabled' => 'disabled')),
            'lieu' => new sfWidgetFormChoice(array('choices'  => $this->getLieuChoices())),
          ));
          
          $this->setValidators(array(
            'appellation' => new sfValidatorString(array('required' => false)),
            'lieu' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getLieuChoices()))),
          ));

          $this->widgetSchema->setNameFormat('ajout_lieu[%s]');
        }

        public function getLieuChoices() {
            if (is_null($this->_lieu_choices)) {
                $this->_lieu_choices = array('' => '');
                foreach ($this->getObject()->getConfig()->filter('^lieu[0-9]') as $key => $item) {
                    if (!$this->getObject()->exist($key)) {
                        $this->_lieu_choices[$key] = $item->getLibelle();
                    }
                }
            }

            return $this->_lieu_choices;
        }

        public function doUpdateObject($values) {
            $this->getObject()->add($values['lieu']);
        }
    }

?>