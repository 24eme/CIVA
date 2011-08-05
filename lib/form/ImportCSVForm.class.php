<?php

class ImportCSVForm extends BaseForm {

    /**
     * 
     */
    public function configure() {
      $this->setWidgets(array(
			      'file'    => new sfWidgetFormInputFile(array('label' => 'Fichier'))
			      ));
      $this->widgetSchema->setNameFormat('csv[%s]');
      
      $this->setValidators(array(
				 'file'    => new sfValidatorFile(array('mime_types' => array('text/plain'),'required' => true))
				 ));
      
      //      $this->validatorSchema['file']->setMessage('required', 'Champ obligatoire');
    }

}
