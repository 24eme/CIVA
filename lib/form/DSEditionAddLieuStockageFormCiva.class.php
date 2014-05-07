<?php

class DSEditionAddLieuStockageFormCiva extends acCouchdbForm 
{
    protected $_ds = null;
    protected $_tiers = null;
    protected $_interpro = null;
    
    public function __construct(DSCiva $ds, $options = array(), $CSRFSecret = null) 
    {
        $this->_ds = $ds;
        $this->_tiers = $this->_ds->getEtablissement();
        $this->_tiers->getLieuxStockage();
        $defaults = array();
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }
    
    public function configure() 
    {
        $this->setWidgets(array(
               'nom' => new sfWidgetFormInputText(),
               'adresse' => new sfWidgetFormInputText(),
               'commune' => new sfWidgetFormInputText(),
               'code_postal' => new sfWidgetFormInputText(),
    	));
        $this->widgetSchema->setLabels(array(
        	'nom' => 'Nom*:',
                'adresse' => 'Adresse*:',
        	'commune' => 'Commune*:',
        	'code_postal' => 'Code Postal*:',
        ));
        $this->setValidators(array(
                'nom' => new sfValidatorString(array('required' => true)),
                'adresse' => new sfValidatorString(array('required' => true)),
                'commune' => new sfValidatorString(array('required' => true)),
                'code_postal' => new sfValidatorString(array('required' => true)),
            ));
        
        $this->widgetSchema->setNameFormat('ds_add_lieu_stockage[%s]');
    }
    
    public function doAddLieuStockage()
    {
        $values = $this->values;
        $this->_tiers->storeLieuStockage($values['nom'],
                                       $values['adresse'],
                                       $values['commune'],
                                       $values['code_postal']);   
        $this->_tiers->save();
        
    }
}