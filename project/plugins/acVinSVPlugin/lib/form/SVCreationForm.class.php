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
        $this->setWidget('type_creation', new sfWidgetFormChoice([
            'expanded' => true,
            'choices' => $this->getTypeCreationChoices()
        ]));

        $this->setValidator('type_creation', new sfValidatorChoice([
            'choices' => array_keys($this->getTypeCreationChoices())
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

    public function save()
    {
        //TODO: faire quelquechose avec le type de création
        // if value === TYPE_DR
        $sv = SVClient::getInstance()->createFromDR($this->identifiant, "2021");
        $sv->save();
        return $sv;
    }
}
