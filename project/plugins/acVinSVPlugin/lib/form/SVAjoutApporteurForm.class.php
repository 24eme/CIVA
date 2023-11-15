<?php

class SVAjoutApporteurForm extends acCouchdbForm
{
    protected $withInfo = false;

    public function __construct(SV $doc, $defaults = [], $options = [], $CSRFSecret = null)
    {
        if (isset($defaults['cvi']) && $defaults['cvi']) {
            $this->withInfo = true;
        }

        parent::__construct($doc, $defaults, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('cvi', new sfWidgetFormInputText());
        $this->setValidator('cvi', new sfValidatorRegex(['pattern' => '/[0-9]{10}|[A-Z]{2}[A-Z0-9]{8,12}/'], ['invalid' => 'Le format CVI n\'est pas correct']));
        $this->widgetSchema->setLabel('cvi', "Ajout ou mise à jour d'un apporteur");

        if ($this->withInfo) {
            $this->setWidget('raison_sociale', new sfWidgetFormInputText());
            $this->setValidator('raison_sociale', new sfValidatorString(['min_length' => 1]));

            $this->setWidget('pays', new sfWidgetFormInputText());
            $this->setValidator('pays', new sfValidatorString(['min_length' => 1]));

            if ($this->isAlsace($this->getDefaults()['cvi'])) {
                $etablissement = EtablissementClient::getInstance()->findByCvi($this->getDefaults()['cvi']);
                if ($etablissement) {
                    $this->setDefault('raison_sociale', $etablissement->raison_sociale);
                    $this->setDefault('pays', $etablissement->siege->pays);
                }
            }
        }

        $this->widgetSchema->setNameFormat('sv_ajout_apporteur[%s]');
    }

    public function save($con = null)
    {
        if (! $this->withInfo) {
            return false;
        }

        $values = $this->getValues();

        if ($this->isAlsace($values['cvi']) === false) {
            $this->getDocument()->addApporteurHorsRegion($values['cvi'], $values['raison_sociale'], $values['pays']);
        } else {
            $dr = DRClient::getInstance()->find('DR-' . $values['cvi'] . '-' . $this->getDocument()->getPeriode());
            $this->getDocument()->addProduitsFromDR($dr);
        }

        $this->getDocument()->save();
    }

    private function isAlsace($cvi)
    {
        return in_array(substr($cvi, 0, 2), ['68', '67']);
    }
}
