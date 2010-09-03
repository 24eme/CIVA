<?php
    class RecolteAjoutLieuForm extends sfCouchdbFormDocumentJson {

        public function setup() {

          if (!($this->getObject() instanceof DRRecolteAppellation)) {
              throw new sfException("Object must be a DRRecolteAppellation object");
          }

          if (!$this->getObject()->hasManyLieu()) {
              throw new sfException("This appellation can't have lieu");
          }

          $lieu_choices = $this->getObject()->getLieuChoices();

	  $this->setWidgets(array(
            'appellation' => new sfWidgetFormChoice(array('choices'  => array($this->getObject()->getKey() => $this->getObject()->getConfig()->getLibelle())), array('disabled' => 'disabled')),
            'lieu' => new sfWidgetFormChoice(array('choices'  => $lieu_choices)),
          ));
          
          $this->setValidators(array(
            'appellation' => new sfValidatorString(array('required' => false)),
            'lieu' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($lieu_choices))),
          ));

          $this->widgetSchema->setNameFormat('ajout_lieu[%s]');
        }

        public function doUpdateObject($values) {
            $this->getObject()->add($values['lieu']);
        }
    }

?>