<?php

class SVMotifModificationForm extends acCouchdbForm
{
    private $sv = null;

    public function __construct(SV $doc, $options = [], $CSRFSecret = null)
    {
        $this->sv = $doc;
        parent::__construct($doc, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('type', new sfWidgetFormChoice(['choices' => $this->sv->getMotifsModification(), 'expanded' => true]));
        $this->setWidget('motif', new sfWidgetFormTextarea());

        $this->setValidator('type', new sfValidatorChoice([
            'choices' => array_keys($this->sv->getMotifsModification())
        ]));
        $this->setValidator('motif', new sfValidatorString(['required' => false]));

        $this->widgetSchema->setNameFormat('sv_motif_modification[%s]');

        $this->validatorSchema->setPostValidator(
            new sfValidatorCallback([
                'callback' => [$this, 'checkMotifNeeded']
            ])
        );
    }

    public function checkMotifNeeded($validator, $values)
    {
        if ($values['type'] === SV::SV_MOTIF_MODIFICATION_AUTRE) {
            if (empty($values['motif'])) {
                throw new sfValidatorError($validator, 'Un motif est requis si la raison de la modification est : Autre');
            }
        }

        return $values;
    }

    public function save()
    {
        $values = $this->getValues();
        $this->sv->setMotifModification($values['type'], $values['motif']);
    }
}
