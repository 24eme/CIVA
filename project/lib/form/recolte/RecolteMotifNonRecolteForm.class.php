<?php

class RecolteMotifNonRecolteForm extends acCouchdbObjectForm {
    public function configure() {

        $tab = $this->getMotifNonRecolteChoices();
        if(!$this->getOption('nonEdel', false) || $this->getObject()->getCepage()->getKey() == 'cepage_ED') unset($tab['ae']);

        $this->setWidgets(array(
            'motif_non_recolte' => new sfWidgetFormChoice(array('choices' => $tab)),
        ));

        $this->setValidators(array(
            'motif_non_recolte' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($tab)))
        ));

        $this->widgetSchema->setNameFormat("recolte_motif[%s]");
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->validatorSchema['motif_non_recolte']->setMessage('required', 'Veuillez sélectionner un motif de non récolte.');
    }

    public function getMotifNonRecolteChoices() {

        $motifs = array_merge(array('' => ''), sfConfig::get('app_configuration_dr_motifs_non_recolte'));

        if($this->getObject()->getDocument()->campagne >= 2020) {
            unset($motifs["AE"]);
        }

        return $motifs;
    }
}
