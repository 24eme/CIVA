<?php
class CompteDroitForm extends acCouchdbForm {

    public function __construct(acCouchdbDocument $doc, $defaults = array(), $options = array(), $CSRFSecret = null) {
        parent::__construct($doc, array("droits" => $doc->droits->toArray()), $options, $CSRFSecret);
    }

    public function configure() {
        $this->setWidgets(array(
                'droits' => new sfWidgetFormChoice(array('choices' => $this->getDroits(), 'expanded' => true, 'multiple' => true)),
        ));

        $this->widgetSchema->setLabels(array(
                'droits' => 'Droits',
        ));

        $this->setValidators(array(
                'droits'  => new sfValidatorChoice(array('required' => false, 'multiple' => true, "choices" => array_keys($this->getDroits()) )),
        ));
        $this->widgetSchema->setNameFormat('compte_droit[%s]');
    }

    public function getDroits() {

        return array();
    }
}
