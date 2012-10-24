<?php

class  RecapitulatifForm extends sfCouchdbFormDocumentJson {

    public function configure() {

        $lieu = $this->getObject();
    if($lieu->getTotalCaveParticuliere()){
        if( $lieu->dplc != 0 )
        {
            // on met le widget en read only si le dplc est =! 0
            $this->setWidgets(array('usages_industriels_saisi' => new sfWidgetFormInput()));
            $this->widgetSchema['usages_industriels_saisi']->setAttributes(array('value'=> $lieu->dplc , 'readonly' => 'true', 'class'=> 'readonly'));

        }else{
            $this->setWidgets(array(
                'usages_industriels_saisi' => new sfWidgetFormInputFloat(array()),
            ));
        }

        $this->setValidators(array(
            'usages_industriels_saisi' => new sfValidatorNumber(array('required' => false)),
        ));
    }

        //$is_unique_acheteur = $lieu->hasSellToUniqueAcheteur();
        foreach ($lieu->acheteurs as $type => $acheteurs_type) {
            foreach ($acheteurs_type as $cvi => $acheteur) {
                /*if (is_null($acheteur->superficie) && is_null($acheteur->dontdplc)) {
                    if ($is_unique_acheteur) {
                        $acheteur->superficie = $lieu->getTotalSuperficie();
                    }
                    if ($is_unique_acheteur) {
                        $acheteur->dontdplc = $lieu->getDplc();
                    }
                }*/
                $af = new RecapitulatifAcheteurForm($acheteur);
                $this->embedForm($type . '_cvi_' . $cvi, $af);
            }
        }

        $this->getValidatorSchema()->setPostValidator(new ValidatorRecapitulatif(null, array('object' => $this->getObject())));
        $this->widgetSchema->setNameFormat('recapitulatif[%s]');
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
        $lieu = $this->getObject();
        $usages_indus = $values['usages_industriels_saisi'];

        if( isset($values['usages_industriels_saisi']))
            $lieu ->set("usages_industriels_saisi", (float)$usages_indus);
    }
}

?>