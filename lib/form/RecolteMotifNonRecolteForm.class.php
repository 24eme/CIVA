<?php

class RecolteMotifNonRecolteForm extends sfCouchdbFormDocumentJson {
    public function configure() {
        $this->setWidgets(array(
            'motif_non_recolte' => new sfWidgetFormChoice(array('choices' => $this->getMotifNonRecolteChoices())),
        ));

        $this->setValidators(array(
            'motif_non_recolte' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getMotifNonRecolteChoices())))
        ));

        $this->widgetSchema->setNameFormat("recolte_motif[%s]");
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }

    public function getMotifNonRecolteChoices() {
        return array_merge(array('' => ''), ConfigurationClient::getConfiguration()->motif_non_recolte->toArray());
    }
}