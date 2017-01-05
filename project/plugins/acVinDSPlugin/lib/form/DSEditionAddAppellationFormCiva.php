<?php

class DSEditionAddAppellationFormCiva extends acCouchdbForm
{
    protected $_ds = null;
    protected $_interpro = null;
    protected $_choices;

    public function __construct(DS $ds, $options = array(), $CSRFSecret = null)
    {
        $this->_ds = $ds;
        $defaults = array();
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('hashref', new sfWidgetFormChoice(array('choices' => $this->getChoices())));
        $this->widgetSchema->setLabel('hashref', 'Séléctionnez une appellation :');
        $this->setValidator('hashref', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getChoices())),array('required' => "Aucune appellation n'a été choisi !")));

        $this->widgetSchema->setNameFormat('ds_add_appellation[%s]');
    }

    public function getChoices()
    {
        if (is_null($this->_choices)) {
            $this->_choices = array("" => "");
            foreach($this->getAppellations() as $key => $appellation) {
                $hash = str_replace("recolte", "declaration", HashMapper::inverse($appellation->getHash()));
                if($this->_ds->exist(preg_replace('/^\/recolte/','declaration', $hash))) {

                    continue;
                }

                $this->_choices[$hash] = $appellation->getLibelle();
            }
        }

        return $this->_choices;
    }

    public function getAppellations()
    {
        return DSCivaClient::getInstance()->getConfigAppellations($this->_ds->getConfig());
    }
}
