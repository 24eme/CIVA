<?php

class LieuDitForm extends acCouchdbForm {

    public function setDefaults($defaults)
    {
        $defaults["appellations"] = array("lieux_vtsgn" => array());
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
        $defaults = $this->getDefaults();

        $formAppellations = new BaseForm();

        foreach ($this->getDocument()->recolte->getAppellations() as $appellation) {
            if (!$appellation->getConfig()->hasManyLieu()) {
                continue;
            }

            $formAppellation = new BaseForm();

            $formAppellation->setWidget('ajout', new sfWidgetFormChoice(array("choices" => $appellation->getLieuChoices())));
            $formAppellation->setValidator('ajout', new sfValidatorChoice(array("required" => false, "choices" => array_keys($appellation->getLieuChoices()))));

            $formLieux = new BaseForm();
            foreach($appellation->mention->getLieux() as $lieu) {
                $formLieux->setWidget($lieu->getKey(), new sfWidgetFormInputCheckbox(array("value_attribute_value" => "1")));
                $formLieux->setValidator($lieu->getKey(), new sfValidatorBoolean());

                if(!$this->getDocument()->getConfigurationCampagne()->exist(HashMapper::convert($appellation->getHash()."/mentionVT/".$lieu->getKey())) ) {
                    $formLieux->getWidget($lieu->getKey())->setAttribute('disabled', 'disabled');
                }
            }
            $formLieux->setDefaults($defaults["appellations"]["lieux_vtsgn"]);
            $formAppellation->embedForm('lieux_vtsgn', $formLieux);


            $formAppellations->embedForm($appellation->getHash(), $formAppellation);
        }

        $this->embedForm('appellations', $formAppellations);

        $this->widgetSchema->setNameFormat('lieudit[%s]');
    }

    public function hasOneLieuForEach() {
        foreach ($this->getDocument()->recolte->getAppellations() as $appellation) {
            if (!$appellation->getConfig()->hasManyLieu()) {
                continue;
            }

            if(count($appellation->getLieux()) < 1) {
                return false;
            }
        }

        return true;
    }

    public function doUpdateObject($values) {
        foreach($values['appellations'] as $hashAppellation => $valuesAppellation) {
            foreach($valuesAppellation["lieux_vtsgn"] as $keyLieu => $valueLieu) {
                foreach($this->getDocument()->get($hashAppellation)->mentions as $item) {
                    if($item->getKey() == "mention") {

                        continue;
                    }
                    if(!$this->getDocument()->getConfigurationCampagne()->exist(HashMapper::convert($item->getHash()."/".$keyLieu))) {

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
                if($item->getKey() != "mention") {

                    continue;
                }
                $keyLieu = $valuesAppellation['ajout'];
                if(!$this->getDocument()->getConfigurationCampagne()->exist(HashMapper::convert($item->getHash()."/".$keyLieu))) {

                    continue;
                }
                $item->getChildrenNode()->add($keyLieu);
            }
        }
    }

    public function save() {
        $this->doUpdateObject($this->getValues());
        $this->getDocument()->save();
    }

}
