<?php

class LieuDitForm extends acCouchdbForm {

    public function setDefaults($defaults)
    {
        foreach ($this->getDocument()->recolte->getAppellations() as $appellation) {
            if (!$appellation->getConfig()->hasManyLieu()) {
                continue;
            }

            foreach($appellation->getLieux() as $lieu) {
                if($lieu->getMention()->getKey() == "mention") {
                    continue;
                }

                $defaults["appellations"]["lieux_vtsgn"][$lieu->getKey()] = true;
            }
        }

        parent::setDefaults($defaults);
    }

    public function setup() {
        //$lieu_choices = $this->getObject()->getLieuChoices();
        $defaults = $this->getDefaults();

        $formAppellations = new BaseForm();

        foreach ($this->getDocument()->recolte->getAppellations() as $appellation) {
            if (!$appellation->getConfig()->hasManyLieu()) {
                continue;
            }

            $formAppellation = new BaseForm();

            $formAppellation->setWidget('ajout', new sfWidgetFormChoice(array("choices" => $appellation->getLieuChoices())));
            $formAppellation->setValidator('ajout', new sfValidatorPass());

            $formLieux = new BaseForm();
            foreach($appellation->mention->getLieux() as $lieu) {
                $formLieux->setWidget($lieu->getKey(), new sfWidgetFormInputCheckbox(array("value_attribute_value" => "1")));
                if(count($lieu->getProduitsDetails())) {
                    $formLieux->getWidget($lieu->getKey())->setAttribute('readonly', 'readonly');
                }
                $formLieux->setValidator($lieu->getKey(), new sfValidatorBoolean());
            }
            $formLieux->setDefaults($defaults["appellations"]["lieux_vtsgn"]);
            $formAppellation->embedForm('lieux_vtsgn', $formLieux);


            $formAppellations->embedForm($appellation->getHash(), $formAppellation);
        }

        $this->embedForm('appellations', $formAppellations);



	    /*$this->setWidgets(array(
            'lieu' => new sfWidgetFormChoice(array( 'choices'  => $lieu_choices, ))));

            $this->setValidators(array(
                'lieu' => new sfValidatorChoice(array('required' => $this->getOption('lieu_required', true), 'choices' => array_keys($lieu_choices))),
            ));*/

            $this->widgetSchema->setNameFormat('lieudit[%s]');
    }



    public function doUpdateObject($values) {
        foreach($values['appellations'] as $hashAppellation => $valuesAppellation) {
            foreach($valuesAppellation["lieux_vtsgn"] as $keyLieu => $valueLieu) {
                foreach($this->getDocument()->get($hashAppellation)->mentions as $item) {
                    if($item->getKey() == "mention") {

                        continue;
                    }
                    if($valueLieu) {
                        $item->getChildrenNode()->add($keyLieu);
                    } elseif($item->getChildrenNode()->exist($keyLieu) && !count($item->getChildrenNode()->get($keyLieu)->getProduitsDetails())) {
                        $item->getChildrenNode()->remove($keyLieu);
                    }
                }

            }
        }
        foreach($values['appellations'] as $hashAppellation => $valuesAppellation) {
            if (!isset($valuesAppellation['ajout'])) {
                continue;
            }
            foreach($this->getDocument()->get($hashAppellation)->mentions as $item) {
                $item->getChildrenNode()->add($valuesAppellation['ajout']);
            }
        }
    }

    public function save() {
        $this->doUpdateObject($this->getValues());
        $this->getDocument()->save();
    }

}
