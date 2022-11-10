<?php

class SVCreationForm extends BaseForm
{
    protected $identifiant = null;
    protected $millesime = null;

    public function __construct($identifiant, $millesime)
    {
        $this->identifiant = $identifiant;
        $this->millesime = $millesime;

        parent::__construct();
    }

    public function configure()
    {
        $this->setWidget('file', new sfWidgetFormInputFile(array('label' => 'Fichier')));
        $this->setValidator('file', new ValidatorImportCsv(array('file_path' => sfConfig::get('sf_data_dir').'/upload', 'required' => false)));

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
            'CSV' => 'Démarrer à partir d\'un fichier',
            'DR' => 'Démarrer depuis les données de la DR',
            'VIERGE' => 'Démarrer avec un document vierge'
        ];
    }

    public function save()
    {
        if($this->getValue('file')) {
            $sv = SVClient::getInstance()->createFromCSV($this->identifiant, "2021", file_get_contents($this->getValue('file')->getTempName()));
        } else {
            $sv = SVClient::getInstance()->createFromDR($this->identifiant, "2021");
        }

        $sv->save();

        return $sv;
    }
}
