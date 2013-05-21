<?php

class DSEditionAddProduitFormCiva extends acCouchdbForm 
{
    protected $_ds = null;
    protected $_interpro = null;
    protected $_config_noeud = null;
    protected $_choices_produits;
    
    public function __construct(DS $ds, ConfigurationAbstract $config_noeud, $options = array(), $CSRFSecret = null) 
    {
        $this->_ds = $ds;
        $this->_config_noeud = $config_noeud;
        $defaults = array();
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }
    
    public function configure() 
    {
        $this->setWidget('hashref', new sfWidgetFormChoice(array('choices' => $this->getChoices())));
        $this->widgetSchema->setLabel('hashref', 'Séléctionnez un produit :');
        $this->setValidator('hashref', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getProduits())),array('required' => "Aucun produit n'a été saisi !")));

        if($this->_config_noeud->hasLieuEditable()) {
            $this->setWidget('lieudit', new sfWidgetFormInput());
            $this->widgetSchema->setLabel('lieudit', 'Saissisez un lieu-dit :');
            $this->setValidator('lieudit', new sfValidatorString());
        }

        $this->widgetSchema->setNameFormat('ds_add_produit[%s]');
    }
    
    public function getChoices() 
    {
        if (is_null($this->_choices_produits)) {
            $this->_choices_produits = array("" => "");
            foreach($this->getProduits() as $hash => $cepage) {
                if(!$this->_config_noeud->hasLieuEditable() && $this->_ds->exist(preg_replace('/^\/recolte/','declaration',$hash))) {

                    continue;
                }
                $this->_choices_produits[$hash] = $cepage->getLibelle();
            }
        }

        return $this->_choices_produits;
    }

    public function getProduits() 
    {
        return $this->_config_noeud->getProduits();
    }
}