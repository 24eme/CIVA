<?php
class AdminStatistiquesMercurialesForm extends BaseForm {
    protected $typesFiltres = [
        'C' => "Coopérative vers Négoce (C)",
        'M' => "Viticulteur vers Négoce (M)",
        'V' => "Vigneron vers Vigneron (V)",
        'X' => "Négoce vers Négoce (X)",
        'I' => "Contrat interne (I)",
        'CR' => "Avec crémant (CR)"
    ];

    public function configure() {
        $this->setWidgets(array(
            'start_date' => new sfWidgetFormInputText(),
            'end_date' => new sfWidgetFormInputText(),
            'filtres'   => new sfWidgetFormChoice(array('choices' => $this->typesFiltres, 'expanded' => true, 'multiple' => true, 'renderer_options' => array('formatter' => array($this, 'formatter')))),
        ));
        $this->widgetSchema->setLabels(array(
	       'start_date' => 'Date début :',
	       'end_date' => 'Date fin :',
	       'filtres' => 'Filtrer par :'
        ));
        $this->setValidators(array(
	       'start_date' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => true), array('invalid' => 'Format valide : dd/mm/aaaa')),
           'end_date' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => true), array('invalid' => 'Format valide : dd/mm/aaaa')),
           'filtres' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->typesFiltres), 'multiple' => true)),
        ));

        $this->widgetSchema->setNameFormat('statistiquesMercuriales[%s]');
    }

    public function formatter($widget, $inputs) {
        $rows = array();
        foreach ($inputs as $input) {
            $rows[] = $widget->renderContentTag('li', $input['input'] . $this->getOption('label_separator') . $input['label']);
        }
    
        return!$rows ? '' : implode($widget->getOption('separator'), $rows);
    }
}
