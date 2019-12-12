<?php
class AdminStatistiquesMercurialesForm extends BaseForm {
    protected $typesMercuriales = [
        'C' => "Coopérative vers Négoce",
        'M' => "Viticulteur vers Négoce",
        'V' => "Vigneron vers Négoce",
        'X' => "Négoce vers Négoce",
        'I' => "Contrat interne",
    ];

    public function configure() {
        $this->setWidgets(array(
            'start_date' => new sfWidgetFormInputText(),
            'end_date' => new sfWidgetFormInputText(),
            'mercuriale'   => new sfWidgetFormChoice(array('choices' => $this->typesMercuriales, 'expanded' => true, 'multiple' => true)),
        ));
        $this->widgetSchema->setLabels(array(
	       'start_date' => 'Date début :',
	       'end_date' => 'Date fin :',
	       'mercuriale' => 'Filtrer par :'
        ));
        $this->setValidators(array(
	       'start_date' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => true), array('invalid' => 'Format valide : dd/mm/aaaa')),
           'end_date' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => true), array('invalid' => 'Format valide : dd/mm/aaaa')),
           'mercuriale' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->typesMercuriales), 'multiple' => true)),
        ));

        $this->widgetSchema->setNameFormat('statistiquesMercuriales[%s]');
        $this->validatorSchema->setPostValidator(new ValidatorAdminStatistiquesMercuriales());
    }
}
