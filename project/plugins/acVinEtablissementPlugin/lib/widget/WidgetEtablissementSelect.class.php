<?php

class WidgetEtablissementSelect extends sfWidgetFormSelect
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

        $this->addOption('familles', array());
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
        $familles = $this->getOption('familles');
		$interpro_id = $this->getOption('interpro_id');
        if (!is_array($familles) && $familles) {
            $familles = array($familles);
        }

        if (is_array($familles) && count($familles) > 0) {

            return sfContext::getInstance()->getRouting()->generate('etablissement_autocomplete_byfamilles', array('interpro_id' => $interpro_id, 'familles' => implode("|",$familles)));
        }

        return sfContext::getInstance()->getRouting()->generate('etablissement_autocomplete_all', array('interpro_id' => $interpro_id));
    }

    protected function getOptionsForSelect($value, $choices)
    {
        if($value) {
            $etablissements = EtablissementAllView::getInstance()->findByEtablissement($value);
            foreach($etablissements as $key => $etablissement) {
                $choices[$value] = $etablissement->key[EtablissementAllView::KEY_NOM];
            }
        }

        return parent::getOptionsForSelect($value, $choices);
    }

}
