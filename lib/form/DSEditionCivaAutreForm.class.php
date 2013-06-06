<?php

class DSEditionCivaAutreForm extends acCouchdbObjectForm {

     public function configure() {  
         
        $this->setWidget('rebeches', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('rebeches', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('rebeches', 'Rebêches');
        
        $this->setWidget('dplc', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('dplc', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('dplc', 'Dépassement de rendements');
        
        $this->setWidget('lies', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('lies', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('lies', 'Lies en Stocks');
        
        $this->setWidget('mouts', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('mouts', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('mouts', 'Moûts concentrés rectifiés');
        
        $this->widgetSchema->setNameFormat('ds[%s]');
    }
    
}
