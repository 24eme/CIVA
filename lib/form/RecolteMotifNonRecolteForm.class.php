<?php

class RecolteMotifNonRecolteForm extends sfCouchdbFormDocumentJson {
    public function configure() {
        
        $tab = $this->getMotifNonRecolteChoices();
        if(!$this->getOption('nonEdel', false)) unset($tab['ae']);

        $this->setWidgets(array(
            'motif_non_recolte' => new sfWidgetFormChoice(array('choices' => $tab)),
        ));

        $this->setValidators(array(
            'motif_non_recolte' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($tab)))
        ));

        $this->widgetSchema->setNameFormat("recolte_motif[%s]");
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    }

    public function getMotifNonRecolteChoices() {
        return array_merge(array('' => ''), ConfigurationClient::getConfiguration()->motif_non_recolte->toArray());
    }
}