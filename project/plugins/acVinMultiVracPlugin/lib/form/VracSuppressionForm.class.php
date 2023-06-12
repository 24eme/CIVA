<?php
class VracSuppressionForm extends acCouchdbObjectForm 
{    
	public function configure()
    {
        $this->setWidgets(array(
        	'motif_suppression' => new sfWidgetFormTextarea()
    	));
        $this->widgetSchema->setLabels(array(
        	'motif_suppression' => 'Motif de suppression*:'
        ));
        $this->setValidators(array(
        	'motif_suppression' => new sfValidatorString(array('required' => true))
        ));

        $this->validatorSchema['motif_suppression']->setMessage('required', "La saisie d'un motif de suppression est obligatoire");

        $this->widgetSchema->setNameFormat('vrac_suppression[%s]');
    }
    
    public function doUpdateObject($values) 
    {
    	parent::doUpdateObject($values);
    }
}