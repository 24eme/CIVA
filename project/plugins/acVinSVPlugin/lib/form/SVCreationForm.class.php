<?php

class SVCreationForm extends BaseForm
{
    protected $cvi = null;

    public function __construct($cvi)
    {
        $this->cvi = $cvi;
        parent::__construct();
    }

    public function configure()
    {
        $this->setWidget('file', new sfWidgetFormInputFile(array('label' => 'Fichier')));
        $this->setValidator('file', new ValidatorImportSVFile(array('file_path' => sfConfig::get('sf_data_dir').'/upload', 'required' => false, 'cvi' => $this->cvi)));

        $this->setWidget('type_creation', new sfWidgetFormChoice([
            'expanded' => true,
            'choices' => $this->getTypeCreationChoices()
        ]));

        $this->setValidator('type_creation', new sfValidatorChoice([
            'choices' => array_keys($this->getTypeCreationChoices()),
            'required' => false
        ]));

        $this->widgetSchema->setNameFormat('sv_creation[%s]');
    }

    protected function getTypeCreationChoices()
    {
        return [
            'DR' => 'Démarrer depuis les données de la DR',
            'CSV' => 'Démarrer à partir d\'un fichier',
            'VIERGE' => 'Démarrer avec un document vierge'
        ];
    }

    public function process()
    {
        if ($this->getValue('type_creation') === 'CSV') {
            return ($this->getValue('file')) ? $this->getValue('file')->getMd5() : null;
        }

        return $this->getValue('type_creation');
    }
}
