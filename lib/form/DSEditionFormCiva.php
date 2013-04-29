<?php

class DSEditionFormCiva extends DSEditionForm {

    protected $ds = null;

    public function __construct(acCouchdbJson $ds, $defaults = array(), $options = array(), $CSRFSecret = null) {

        $this->ds = $ds;
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure() {
        parent::configure();
        foreach ($this->ds->getProduits() as $key => $declaration) {
	  $this->setWidget('vt_' . $key, new sfWidgetFormInputFloat(array(), array('size' => '6')));
	  $this->setValidator('vt_' . $key, new sfValidatorNumber(array('required' => false)));
	  $this->widgetSchema->setLabel('vt_' . $key, 'VT');

	  $this->setWidget('sgn_' . $key, new sfWidgetFormInput(array(), array('size' => '6')));
	  $this->setValidator('sgn_' . $key, new sfValidatorNumber(array('required' => false)));
	  $this->widgetSchema->setLabel('sgn_' . $key, 'SGN');
        }

        $this->widgetSchema->setNameFormat('ds[%s]');
    }

    public function doUpdateObject() {
        parent::doUpdateObject();
        $values = $this->values;
        foreach ($values as $prodKey => $volumeRev) {
            if ($prodKey == 'commentaire') {
                $this->getDocument()->commentaire = $volumeRev;
            } else {
	      if (substr($prodKey, 0, strlen('vt_')) === 'vt_'){ 
		$this->updateVT(substr($prodKey,strlen('vt_')), $volumeRev);
              }
	      if (substr($prodKey, 0, strlen('sgn_')) === 'sgn_')
		$this->updateSGN(substr($prodKey,strlen('sgn_')), $volumeRev);
            }
        }
    }

       
    public function updateVT($prodKey, $volumeRev) {
        if ($this->getDocument()->declarations->exist($prodKey)) {
            $this->getDocument()->declarations[$prodKey]->vt = $volumeRev;
        }
    }
    
    public function updateSGN($prodKey, $volumeRev) {
        if ($this->getDocument()->declarations->exist($prodKey)) {
            $this->getDocument()->declarations[$prodKey]->sgn = $volumeRev;
        }
    }
    
    public function setDefaults($defaults) {
        parent::setDefaults($defaults);
        foreach ($this->ds->getProduits() as $key => $value) {
	  if($value->exist('vt')) 
              $defaults['vt_'.$key] = $value->vt;
	  if($value->exist('sgn')) 
              $defaults['sgn_'.$key] = $value->sgn;
        }
    }

}
