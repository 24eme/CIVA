<?php

class DSEditionAddLieuFormCiva extends acCouchdbForm
{
    protected $_ds = null;
    protected $_interpro = null;
    protected $_config_noeud = null;
    protected $_choices;

    public function __construct(DS $ds, ConfigurationAppellation $config_noeud, $options = array(), $CSRFSecret = null)
    {
        $this->_ds = $ds;
        $this->_config_noeud = $config_noeud;
        $defaults = array();
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('hashref', new sfWidgetFormChoice(array('choices' => $this->getChoices())));
        $this->widgetSchema->setLabel('hashref', 'Séléctionnez un lieu-dit :');
        $this->setValidator('hashref', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getChoices())),array('required' => "Aucun lieu n'a été choisi !")));

        $this->widgetSchema->setNameFormat('ds_add_lieu[%s]');
    }

    public function getChoices()
    {
        if (is_null($this->_choices)) {
            $this->_choices = array("" => "");
            foreach($this->getLieux() as $hash => $lieu) {
                if($this->_ds->exist($hash)) {

                    continue;
                }
                $this->_choices[$hash] = $lieu->getLibelle();
            }
        }

        return $this->_choices;
    }

    public function getLieux()
    {
        $lieux = array();

        foreach($this->_config_noeud->mentions->getFirst()->getLieux() as $lieu) {
            $lieux[HashMapper::inverse($lieu->getHash(), 'DS')] = $lieu;
        }

        return $lieux;
    }

    public function hasLieuEditable(){
        return $this->_config_noeud->hasLieuEditable();
    }
}
