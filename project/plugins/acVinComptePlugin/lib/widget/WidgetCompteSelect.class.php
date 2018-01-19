<?php

class WidgetCompteSelect extends sfWidgetFormSelect
{
    protected $identifiant = null;

    public function __construct($options = array(), $attributes = array())
    {
        parent::__construct($options, $attributes);

        $this->setAttribute('data-ajax', $this->getUrlAutocomplete());
    }

    protected function configure($options = array(), $attributes = array())
    {
        parent::configure($options, $attributes);

        $this->addOption('autofocus', array());
        $this->addOption('choices', array());
        $this->addRequiredOption('interpro_id', 'INTERPRO-declaration');
    }

    public function setOption($name, $value) {
        parent::setOption($name, $value);

        if($name == 'familles') {
            $this->setAttribute('data-ajax', $this->getUrlAutocomplete());
        }

        if($name == 'autofocus') {
            $this->setAttribute('autofocus','autofocus');
        }

        return $this;
    }

    public function getUrlAutocomplete() {
		$interpro_id = $this->getOption('interpro_id');

        return sfContext::getInstance()->getRouting()->generate('compte_autocomplete_all', array('interpro_id' => $interpro_id));
    }

    protected function getOptionsForSelect($value, $choices)
    {
        if($value) {
            $comptes = CompteAllView::getInstance()->findByCompte($value);
            foreach($etablissements as $key => $etablissement) {
                $choices[$value] = $compte->key[EtablissementAllView::KEY_NOM];
            }
        }

        return parent::getOptionsForSelect($value, $choices);
    }

}
