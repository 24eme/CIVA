<?php

class LieuDitForm extends acCouchdbObjectForm {

    public function setup() {
        $lieu_choices = $this->getObject()->getLieuChoices();
	    $this->setWidgets(array(
            'lieu' => new sfWidgetFormChoice(array( 'choices'  => $lieu_choices, ))));

            $this->setValidators(array(
                'lieu' => new sfValidatorChoice(array('required' => $this->getOption('lieu_required', true), 'choices' => array_keys($lieu_choices))),
            ));

            $this->widgetSchema->setNameFormat('lieudit_'.$this->getObject()->getKey().'[%s]');
            $this->validatorSchema['lieu']->setMessage('required', 'Champ obligatoire');
    }

    public function doUpdateObject($values) {
        if (isset($values['lieu'])) {
            /*if($mention->getConfig()->exist($values['lieu'])){
                    $lieu = $mention->add($values['lieu']);
                    foreach ($lieu->getConfig()->getChildrenNode() as $k => $v) {

                    }
                }
            }*/
            foreach($this->getObject()->getChildrenNode() as $item) {
                $this->getObject()->getChildrenNode()->add($item->getKey())->getChildrenNode()->add($values['lieu']);
            }
        }
    }

}
