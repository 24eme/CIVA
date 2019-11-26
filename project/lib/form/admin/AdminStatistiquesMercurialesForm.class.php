<?php
class AdminStatistiquesMercurialesForm extends BaseForm {
    public function configure() {
        $mercurialesTypes = array(null => null, "C" => "C", "M" => "M", "V" => "V", "X" => "X");
        $this->setWidgets(array(
		   'start_date' => new sfWidgetFormInputText(),
		   'end_date' => new sfWidgetFormInputText(),
           'mercuriale'   => new sfWidgetFormChoice(array('choices' => $mercurialesTypes)),
        ));
        $this->widgetSchema->setLabels(array(
	       'start_date' => 'Date début :',
	       'end_date' => 'Date fin :',
	       'mercuriale' => 'Type mercuriale :'
        ));
        $this->setValidators(array(
	       'start_date' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => true), array('invalid' => 'Format valide : dd/mm/aaaa')),
           'end_date' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => true), array('invalid' => 'Format valide : dd/mm/aaaa')),
           'mercuriale' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($mercurialesTypes))),
        ));

        $this->widgetSchema->setNameFormat('statistiquesMercuriales[%s]');
        $this->validatorSchema->setPostValidator(new ValidatorAdminStatistiquesMercuriales());
    }
}